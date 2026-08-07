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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();

            // Modifié : category_id devient nullable et met à null au lieu de supprimer en cascade
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            // Projet / Étude lié
            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            // Colonne pour l'étape du projet
            $table->unsignedBigInteger('project_step_id')->nullable();

            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            $table->integer('progress')->default(0);

            $table->integer('ordre')->default(999);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};