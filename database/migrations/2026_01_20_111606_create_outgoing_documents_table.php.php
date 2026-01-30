<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outgoing_documents', function (Blueprint $table) {
            $table->id();

            // ✅ Σειριακό Α/Α εξερχομένου
            $table->unsignedInteger('aa')->nullable();

            // ✅ Αν είναι απάντηση σε εισερχόμενο
            $table->foreignId('reply_to_incoming_id')
                  ->nullable()
                  ->constrained('incoming_documents')
                  ->nullOnDelete();

            $table->string('protocol_number')->nullable();
            $table->string('incoming_protocol')->nullable();
            $table->date('incoming_date')->nullable();

            $table->string('subject')->nullable();
            $table->string('sender')->nullable();
            $table->date('document_date')->nullable();

            $table->string('incoming_document_number')->nullable();

            $table->text('summary')->nullable();
            $table->text('comments')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outgoing_documents');
    }
};
