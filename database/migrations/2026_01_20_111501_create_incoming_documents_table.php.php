<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_documents', function (Blueprint $table) {
            $table->id();

            // ✅ Σειριακό Α/Α εισερχομένου
            $table->unsignedInteger('aa')->unique();

            $table->string('protocol_number')->nullable();
            $table->string('incoming_protocol')->nullable();
            $table->date('incoming_date')->nullable();

            $table->string('subject')->nullable();
            $table->string('sender')->nullable();
            $table->date('document_date')->nullable();

            $table->text('summary')->nullable();
            $table->text('comments')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_documents');
    }
};
