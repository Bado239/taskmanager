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
        Schema::table('course_resources', function (Blueprint $table) {

            $table->integer('relevance')
                ->default(0)
                ->after('score');


            $table->boolean('saved')
                ->default(false)
                ->after('relevance');


            $table->timestamp('searched_at')
                ->nullable()
                ->after('saved');

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_resources', function (Blueprint $table) {
            //
        });
    }
};
