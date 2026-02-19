<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // -----------------------
        // incoming_documents
        // -----------------------
        if (Schema::hasColumn('incoming_documents', 'incoming_protocol')
            && ! Schema::hasColumn('incoming_documents', 'incoming_document_number')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('incoming_protocol', 'incoming_document_number');
            });
        }

        if (Schema::hasColumn('incoming_documents', 'sender')
            && ! Schema::hasColumn('incoming_documents', 'issue_place')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('sender', 'issue_place');
            });
        }

        if (Schema::hasColumn('incoming_documents', 'subject')
            && ! Schema::hasColumn('incoming_documents', 'issuing_authority')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('subject', 'issuing_authority');
            });
        }

        if (Schema::hasColumn('incoming_documents', 'comments')
            && ! Schema::hasColumn('incoming_documents', 'archive_folder')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('comments', 'archive_folder');
            });
        }

        // -----------------------
        // outgoing_documents
        // -----------------------
        if (Schema::hasColumn('outgoing_documents', 'incoming_document_number')
            && ! Schema::hasColumn('outgoing_documents', 'related_numbers')) {
            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->renameColumn('incoming_document_number', 'related_numbers');
            });
        }

        if (Schema::hasColumn('outgoing_documents', 'incoming_protocol')
            && ! Schema::hasColumn('outgoing_documents', 'archive_folder')) {
            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->renameColumn('incoming_protocol', 'archive_folder');
            });
        }

        if (Schema::hasColumn('outgoing_documents', 'sender')
            && ! Schema::hasColumn('outgoing_documents', 'recipient_authority')) {
            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->renameColumn('sender', 'recipient_authority');
            });
        }

        // Προαιρετικό (δεν στο προτείνω τώρα, το αφήνω σαν σχόλιο):
        // incoming_date -> sent_date
        // if (Schema::hasColumn('outgoing_documents', 'incoming_date')
        //     && ! Schema::hasColumn('outgoing_documents', 'sent_date')) {
        //     Schema::table('outgoing_documents', function (Blueprint $table) {
        //         $table->renameColumn('incoming_date', 'sent_date');
        //     });
        // }
    }

    public function down(): void
    {
        // -----------------------
        // incoming_documents (reverse)
        // -----------------------
        if (Schema::hasColumn('incoming_documents', 'incoming_document_number')
            && ! Schema::hasColumn('incoming_documents', 'incoming_protocol')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('incoming_document_number', 'incoming_protocol');
            });
        }

        if (Schema::hasColumn('incoming_documents', 'issue_place')
            && ! Schema::hasColumn('incoming_documents', 'sender')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('issue_place', 'sender');
            });
        }

        if (Schema::hasColumn('incoming_documents', 'issuing_authority')
            && ! Schema::hasColumn('incoming_documents', 'subject')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('issuing_authority', 'subject');
            });
        }

        if (Schema::hasColumn('incoming_documents', 'archive_folder')
            && ! Schema::hasColumn('incoming_documents', 'comments')) {
            Schema::table('incoming_documents', function (Blueprint $table) {
                $table->renameColumn('archive_folder', 'comments');
            });
        }

        // -----------------------
        // outgoing_documents (reverse)
        // -----------------------
        if (Schema::hasColumn('outgoing_documents', 'related_numbers')
            && ! Schema::hasColumn('outgoing_documents', 'incoming_document_number')) {
            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->renameColumn('related_numbers', 'incoming_document_number');
            });
        }

        if (Schema::hasColumn('outgoing_documents', 'archive_folder')
            && ! Schema::hasColumn('outgoing_documents', 'incoming_protocol')) {
            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->renameColumn('archive_folder', 'incoming_protocol');
            });
        }

        if (Schema::hasColumn('outgoing_documents', 'recipient_authority')
            && ! Schema::hasColumn('outgoing_documents', 'sender')) {
            Schema::table('outgoing_documents', function (Blueprint $table) {
                $table->renameColumn('recipient_authority', 'sender');
            });
        }
    }
};

