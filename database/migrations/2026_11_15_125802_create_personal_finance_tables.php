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
        Schema::create('finance_months', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7)->unique();
            $table->unsignedBigInteger('income')->default(500000);
            $table->timestamps();
        });

        Schema::create('finance_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('finance_month_id')
                ->constrained('finance_months')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('kind', 20); // expense ou saving
            $table->unsignedBigInteger('planned_amount')->default(0);
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();
        });

        Schema::create('finance_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('finance_category_id')
                ->constrained('finance_categories')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('amount');
            $table->date('movement_date');
            $table->string('description')->nullable();

            $table->timestamps();

            $table->index('movement_date');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_movements');
        Schema::dropIfExists('finance_categories');
        Schema::dropIfExists('finance_months');
    }
};
