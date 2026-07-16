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
        if (!Schema::hasTable('project_steps')) {
            Schema::create('project_steps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->timestamps();
            });
        }

        // Ajout sécurisé de la clé étrangère sur tasks
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'project_step_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->foreign('project_step_id')
                      ->references('id')
                      ->on('project_steps')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'project_step_id')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropForeign(['project_step_id']);
            });
        }

        Schema::dropIfExists('project_steps');
    }
};