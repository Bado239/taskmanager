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

            $table->integer('rating')
                ->default(0)
                ->after('relevance');

            $table->text('notes')
                ->nullable()
                ->after('rating');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_resources', function (Blueprint $table) {

            $table->dropColumn([
                'rating',
                'notes'
            ]);

        });
    }
};