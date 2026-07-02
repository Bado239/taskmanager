<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            $table->integer('progress')->default(0);

            // ⭐ ajout logique même dans le design du système
            $table->integer('ordre')->default(999);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};