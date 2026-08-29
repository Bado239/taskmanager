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

            $table->string('file_type')
                ->nullable()
                ->after('type');

            $table->boolean('is_university')
                ->default(false)
                ->after('file_type');

            $table->integer('score')
                ->default(0)
                ->after('is_university');

        });
    }

        /**
         * Reverse the migrations.
         */

    public function down(): void
    {
        Schema::table('course_resources', function (Blueprint $table) {

            $table->dropColumn([
                'file_type',
                'is_university',
                'score'
            ]);

        });
    }

};
