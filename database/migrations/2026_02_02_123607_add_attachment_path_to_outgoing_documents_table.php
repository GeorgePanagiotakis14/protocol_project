<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('outgoing_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('outgoing_documents', 'attachment_path')) {
                $table->string('attachment_path')
                      ->nullable()
                      ->after('incoming_protocol');
            }
        });
    }

    public function down(): void
    {
        Schema::table('outgoing_documents', function (Blueprint $table) {
            if (Schema::hasColumn('outgoing_documents', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};
