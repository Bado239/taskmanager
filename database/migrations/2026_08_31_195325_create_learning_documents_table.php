<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('learning_documents', function (Blueprint $table) {

            $table->id();

            // Chapitre concerné
            $table->foreignId('task_id')
                ->constrained()
                ->cascadeOnDelete();


            // Nom du document
            $table->string('title');


            // pdf, word, link
            $table->string('type');


            // Fichier stocké
            $table->string('file_path')
                ->nullable();


            // Lien internet
            $table->text('url')
                ->nullable();


            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('learning_documents');
    }

};