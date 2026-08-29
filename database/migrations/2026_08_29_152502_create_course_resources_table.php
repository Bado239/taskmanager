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
        Schema::create('course_resources', function (Blueprint $table) {

            $table->id();

            // La tâche/chapter concernée
            $table->foreignId('task_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nom du cours
            $table->string('title');

            // Université ou source
            $table->string('source')->nullable();

            // Lien vers le cours
            $table->text('url');

            // Type : PDF, Vidéo, Article...
            $table->string('type')->default('cours');

            // Priorité d'affichage
            $table->integer('order')->default(1);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_resources');
    }
};
