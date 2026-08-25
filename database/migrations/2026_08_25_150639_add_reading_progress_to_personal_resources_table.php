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
        Schema::table('personal_resources', function (Blueprint $table) {

            // Page actuelle du livre
            $table->integer('current_page')
                ->default(1)
                ->after('pdf_path');


            // Pourcentage de progression de lecture
            $table->integer('progress')
                ->default(0)
                ->after('current_page');

        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_resources', function (Blueprint $table) {

            $table->dropColumn([
                'current_page',
                'progress'
            ]);

        });
    }

};