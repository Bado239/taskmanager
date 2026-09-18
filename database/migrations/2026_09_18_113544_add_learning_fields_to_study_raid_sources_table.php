<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('study_raid_sources', function (Blueprint $table) {


            $table->string('level')
                ->default('Master 1')
                ->after('subject');


            $table->string('provider')
                ->default('StudyRaid')
                ->after('url');


        });

    }



    public function down(): void
    {

        Schema::table('study_raid_sources', function (Blueprint $table) {


            $table->dropColumn([
                'level',
                'provider'
            ]);


        });

    }

};