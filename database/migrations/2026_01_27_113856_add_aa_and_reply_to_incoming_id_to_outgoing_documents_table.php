<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outgoing_documents', function (Blueprint $table) {
            $table->unsignedInteger('aa')->after('id')->unique();
            $table->foreignId('reply_to_incoming_id')
                  ->nullable()
                  ->after('aa')
                  ->constrained('incoming_documents')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('outgoing_documents', function (Blueprint $table) {
            $table->dropForeign(['reply_to_incoming_id']);
            $table->dropColumn(['aa', 'reply_to_incoming_id']);
        });
    }
};
