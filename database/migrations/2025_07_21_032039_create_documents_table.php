<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->year('year')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('publisher')->nullable();
            $table->string('issn')->nullable();
            $table->string('conference_name')->nullable();
            $table->string('publication_link')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
