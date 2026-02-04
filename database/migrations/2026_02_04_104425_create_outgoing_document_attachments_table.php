<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outgoing_document_attachments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('outgoing_document_id')
                ->constrained('outgoing_documents')
                ->cascadeOnDelete();

            $table->string('path'); // storage path στο public disk
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();

            $table->index('outgoing_document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outgoing_document_attachments');
    }
};
