<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $defaultYear = (int) date('Y');

        // 1) Add protocol_year to documents
        Schema::table('incoming_documents', function (Blueprint $table) use ($defaultYear) {
            if (!Schema::hasColumn('incoming_documents', 'protocol_year')) {
                $table->unsignedSmallInteger('protocol_year')->default($defaultYear)->after('aa');
            }
        });

        Schema::table('outgoing_documents', function (Blueprint $table) use ($defaultYear) {
            if (!Schema::hasColumn('outgoing_documents', 'protocol_year')) {
                $table->unsignedSmallInteger('protocol_year')->default($defaultYear)->after('aa');
            }
        });

        // 2) Backfill protocol_year based on available dates (best effort)
        DB::statement("UPDATE incoming_documents SET protocol_year = COALESCE(YEAR(incoming_date), YEAR(created_at), {$defaultYear})");
        DB::statement("UPDATE outgoing_documents SET protocol_year = COALESCE(YEAR(document_date), YEAR(created_at), {$defaultYear})");

        // 3) Fix indexes for year-based AA
        // Incoming had unique(aa) -> now must be unique(protocol_year, aa)
        Schema::table('incoming_documents', function (Blueprint $table) {
            try { $table->dropUnique('incoming_documents_aa_unique'); } catch (\Throwable $e) {}
            try { $table->unique(['protocol_year', 'aa'], 'incoming_documents_protocol_year_aa_unique'); } catch (\Throwable $e) {}
        });

        // Outgoing: NOT unique (γιατί μπορεί να υπάρχουν πολλαπλές απαντήσεις με ίδιο Α/Α)
        Schema::table('outgoing_documents', function (Blueprint $table) {
            try { $table->index(['protocol_year', 'aa'], 'outgoing_documents_protocol_year_aa_index'); } catch (\Throwable $e) {}
        });

        // 4) Make protocol_counters year-based
        Schema::table('protocol_counters', function (Blueprint $table) use ($defaultYear) {
            if (!Schema::hasColumn('protocol_counters', 'year')) {
                $table->unsignedSmallInteger('year')->default($defaultYear)->after('id');
            }
        });

        DB::table('protocol_counters')->where('year', 0)->orWhereNull('year')->update(['year' => $defaultYear]);

        Schema::table('protocol_counters', function (Blueprint $table) {
            try { $table->unique(['year'], 'protocol_counters_year_unique'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('protocol_counters', function (Blueprint $table) {
            if (Schema::hasColumn('protocol_counters', 'year')) {
                $table->dropUnique('protocol_counters_year_unique');
                $table->dropColumn('year');
            }
        });

        Schema::table('outgoing_documents', function (Blueprint $table) {
            if (Schema::hasColumn('outgoing_documents', 'protocol_year')) {
                $table->dropIndex('outgoing_documents_protocol_year_aa_index');
                $table->dropColumn('protocol_year');
            }
        });

        Schema::table('incoming_documents', function (Blueprint $table) {
            if (Schema::hasColumn('incoming_documents', 'protocol_year')) {
                $table->dropUnique('incoming_documents_protocol_year_aa_unique');
                $table->unique(['aa'], 'incoming_documents_aa_unique');
                $table->dropColumn('protocol_year');
            }
        });
    }
};
