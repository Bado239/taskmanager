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

    Schema::table('tasks', function (Blueprint $table) {


        $table->foreignId('study_raid_source_id')
            ->nullable()
            ->after('project_id')
            ->constrained('study_raid_sources')
            ->nullOnDelete();


    });


    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            //
        });
    }
};
