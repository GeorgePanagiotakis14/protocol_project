<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        $dbName = DB::getDatabaseName();

        $rows = DB::select(
            "SELECT 1
             FROM information_schema.statistics
             WHERE table_schema = ?
               AND table_name = ?
               AND index_name = ?
             LIMIT 1",
            [$dbName, $table, $indexName]
        );

        return !empty($rows);
    }

    public function up(): void
    {
        $defaultYear = (int) date('Y');

        /**
         * 1) Add protocol_year (nullable first, then backfill, then default)
         *    Το κάνουμε nullable για να μην σκάσει σε insert/update ενδιάμεσα.
         */
        Schema::table('incoming_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('incoming_documents', 'protocol_year')) {
                $table->unsignedSmallInteger('protocol_year')->nullable()->after('aa');
            }
        });

        Schema::table('outgoing_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('outgoing_documents', 'protocol_year')) {
                $table->unsignedSmallInteger('protocol_year')->nullable()->after('aa');
            }
        });

        /**
         * 2) Backfill protocol_year
         */
        DB::statement("UPDATE incoming_documents
                       SET protocol_year = COALESCE(YEAR(incoming_date), YEAR(created_at), {$defaultYear})
                       WHERE protocol_year IS NULL");

        DB::statement("UPDATE outgoing_documents
                       SET protocol_year = COALESCE(YEAR(document_date), YEAR(created_at), {$defaultYear})
                       WHERE protocol_year IS NULL");

        /**
         * 3) FIX duplicates in incoming (protocol_year, aa) by renumbering aa per year.
         *    Αυτό λύνει το Duplicate entry '2020-1'.
         *    Ταξινομούμε με (protocol_year, aa, id) και δίνουμε νέα aa = 1..N ανά έτος.
         */
        DB::statement("SET @rn := 0");
        DB::statement("SET @py := 0");

        DB::statement("
            UPDATE incoming_documents i
            JOIN (
                SELECT
                    id,
                    protocol_year,
                    (@rn := IF(@py = protocol_year, @rn + 1, 1)) AS new_aa,
                    (@py := protocol_year) AS _py
                FROM incoming_documents
                ORDER BY protocol_year ASC, aa ASC, id ASC
            ) x ON x.id = i.id
            SET i.aa = x.new_aa,
                i.protocol_number = CAST(x.new_aa AS CHAR)
        ");

        /**
         * 4) Fix indexes for year-based AA
         *    - Incoming: unique(protocol_year, aa)
         *    - Outgoing: index(protocol_year, aa) (όχι unique)
         */

        // Drop πιθανά παλιά unique indexes (με διάφορα ονόματα) αν υπάρχουν
        foreach ([
            'incoming_documents_aa_unique',
            'incoming_year_aa_unique',
            'incoming_documents_protocol_year_aa_unique',
            'incoming_documents_incoming_year_aa_unique',
        ] as $idx) {
            if ($this->indexExists('incoming_documents', $idx)) {
                Schema::table('incoming_documents', function (Blueprint $table) use ($idx) {
                    $table->dropUnique($idx);
                });
            }
        }

        // Add the correct unique
        if (!$this->indexExists('incoming_documents', 'incoming_documents_protocol_year_aa_unique')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->unique(['protocol_year', 'aa'], 'incoming_documents_protocol_year_aa_unique');
            });
        }

        // Outgoing index (όχι unique)
        if (!$this->indexExists('outgoing_documents', 'outgoing_documents_protocol_year_aa_index')) {
            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->index(['protocol_year', 'aa'], 'outgoing_documents_protocol_year_aa_index');
            });
        }

        /**
         * 5) protocol_counters: add year column + unique(year)
         *    (Αν το χρησιμοποιείς. Αν δεν το χρησιμοποιείς πλέον, δεν πειράζει – θα είναι safe.)
         */
        if (Schema::hasTable('protocol_counters')) {
            Schema::table('protocol_counters', function (Blueprint $table) {
                if (!Schema::hasColumn('protocol_counters', 'year')) {
                    $table->unsignedSmallInteger('year')->nullable()->after('id');
                }
            });

            // Backfill year if NULL
            DB::table('protocol_counters')
                ->whereNull('year')
                ->update(['year' => $defaultYear]);

            // Αν υπάρχουν πολλά rows με ίδιο year, κράτα το max(current) και σβήσε τα υπόλοιπα
            $dupes = DB::select("
                SELECT year, COUNT(*) c
                FROM protocol_counters
                GROUP BY year
                HAVING c > 1
            ");

            foreach ($dupes as $d) {
                $y = (int) $d->year;
                $maxCurrent = (int) DB::table('protocol_counters')->where('year', $y)->max('current');
                DB::table('protocol_counters')->where('year', $y)->update(['current' => $maxCurrent]);

                $keepId = DB::table('protocol_counters')
                    ->where('year', $y)
                    ->orderBy('id', 'asc')
                    ->value('id');

                DB::table('protocol_counters')
                    ->where('year', $y)
                    ->where('id', '!=', $keepId)
                    ->delete();
            }

            if (!$this->indexExists('protocol_counters', 'protocol_counters_year_unique')) {
                Schema::table('protocol_counters', function (Blueprint $table) {
                    $table->unique(['year'], 'protocol_counters_year_unique');
                });
            }

            // set default + not null (προαιρετικό αλλά καθαρό)
            Schema::table('protocol_counters', function (Blueprint $table) use ($defaultYear) {
                if (Schema::hasColumn('protocol_counters', 'year')) {
                    // Σε MySQL αυτό “αλλάζει” τον ορισμό
                    $table->unsignedSmallInteger('year')->default($defaultYear)->nullable(false)->change();
                }
            });
        }

        /**
         * 6) Τέλος: βάζουμε default στο protocol_year
         */
        Schema::table('incoming_documents', function (Blueprint $table) use ($defaultYear) {
            if (Schema::hasColumn('incoming_documents', 'protocol_year')) {
                $table->unsignedSmallInteger('protocol_year')->default($defaultYear)->nullable(false)->change();
            }
        });

        Schema::table('outgoing_documents', function (Blueprint $table) use ($defaultYear) {
            if (Schema::hasColumn('outgoing_documents', 'protocol_year')) {
                $table->unsignedSmallInteger('protocol_year')->default($defaultYear)->nullable(false)->change();
            }
        });
    }

    public function down(): void
    {
        // protocol_counters
        if (Schema::hasTable('protocol_counters') && Schema::hasColumn('protocol_counters', 'year')) {
            if ($this->indexExists('protocol_counters', 'protocol_counters_year_unique')) {
                Schema::table('protocol_counters', function (Blueprint $table) {
                    $table->dropUnique('protocol_counters_year_unique');
                });
            }

            Schema::table('protocol_counters', function (Blueprint $table) {
                $table->dropColumn('year');
            });
        }

        // outgoing
        if (Schema::hasColumn('outgoing_documents', 'protocol_year')) {
            if ($this->indexExists('outgoing_documents', 'outgoing_documents_protocol_year_aa_index')) {
                Schema::table('outgoing_documents', function (Blueprint $table) {
                    $table->dropIndex('outgoing_documents_protocol_year_aa_index');
                });
            }

            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->dropColumn('protocol_year');
            });
        }

        // incoming
        if (Schema::hasColumn('incoming_documents', 'protocol_year')) {
            if ($this->indexExists('incoming_documents', 'incoming_documents_protocol_year_aa_unique')) {
                Schema::table('incoming_documents', function (Blueprint $table) {
                    $table->dropUnique('incoming_documents_protocol_year_aa_unique');
                });
            }

            // (Προαιρετικά) επαναφορά unique(aa) – μόνο αν το θες
            if (!$this->indexExists('incoming_documents', 'incoming_documents_aa_unique')) {
                Schema::table('incoming_documents', function (Blueprint $table) {
                    $table->unique(['aa'], 'incoming_documents_aa_unique');
                });
            }

            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->dropColumn('protocol_year');
            });
        }
    }
};
