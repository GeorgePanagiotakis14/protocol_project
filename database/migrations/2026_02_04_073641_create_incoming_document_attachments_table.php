<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incoming_document_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('incoming_document_id')
                ->constrained('incoming_documents')
                ->cascadeOnDelete();

            $table->string('path');              // storage path στο public disk
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();

            $table->index('incoming_document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_document_attachments');
    }
};
