<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'category_name')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('category_name')->nullable()->after('category_id');
            });
        }

        // Remplir les tâches existantes avec le nom de la catégorie associée
        \App\Models\Task::whereNotNull('category_id')->whereNull('category_name')->each(function ($task) {
            $category = \App\Models\Category::find($task->category_id);
            if ($category) {
                $task->update(['category_name' => $category->title ?? $category->name]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('category_name');
        });
    }
};