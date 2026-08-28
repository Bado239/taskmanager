<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {

            $table->string('title')
                ->default('Emploi du temps');

            $table->string('type')
                ->nullable();

            $table->text('description')
                ->nullable();

        });
    }


    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {

            $table->dropColumn([
                'title',
                'type',
                'description'
            ]);

        });
    }

};