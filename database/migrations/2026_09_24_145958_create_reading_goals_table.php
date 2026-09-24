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
        Schema::create('reading_goals', function (Blueprint $table) {

            $table->id();


            // Livre concerné
            $table->foreignId('personal_resource_id')
                ->constrained('personal_resources')
                ->cascadeOnDelete();


            // Date de l'objectif
            $table->date('date');


            // Page actuelle au début de l'objectif
            $table->integer('start_page')
                ->default(1);


            // Page à atteindre
            $table->integer('target_page');


            // Statut
            $table->string('status')
                ->default('pending');


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_goals');
    }
};
