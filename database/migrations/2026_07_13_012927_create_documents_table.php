<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Nadu records live in a separate table for each company, so a
            // database foreign key to one shared table cannot be used here.
            $table->unsignedBigInteger('nadu_id')->index();

            $table->string('document_type');
            $table->string('file_name');
            $table->string('file_path');

            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
