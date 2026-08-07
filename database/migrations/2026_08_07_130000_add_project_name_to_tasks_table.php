<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'project_name')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('project_name')->nullable()->after('project_id');
            });
        }

        // Remplir les tâches existantes
        \App\Models\Task::whereNotNull('project_id')->whereNull('project_name')->each(function ($task) {
            $project = \App\Models\Project::find($task->project_id);
            if ($project) {
                $task->update(['project_name' => $project->title]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('project_name');
        });
    }
};