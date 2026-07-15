<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('exam_preps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade'); // Lié à l'activité/cours
            $table->boolean('course_reviewed')->default(false); // Cours lu/assimilé
            $table->boolean('summary_done')->default(false);    // Fiche de révision faite
            $table->boolean('exercises_done')->default(false);  // TD/Exercices pratiques faits
            $table->boolean('past_papers_done')->default(false); // Sujet d'examens d'annales traités
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_preps');
    }
};
