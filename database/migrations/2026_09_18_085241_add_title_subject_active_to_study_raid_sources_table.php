<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::table('study_raid_sources', function (Blueprint $table) {


            $table->string('title')
                ->nullable()
                ->after('id');


            $table->string('subject')
                ->nullable()
                ->after('url');


            $table->boolean('active')
                ->default(true)
                ->after('subject');


        });

    }



    public function down(): void
    {

        Schema::table('study_raid_sources', function (Blueprint $table) {


            $table->dropColumn([
                'title',
                'subject',
                'active'
            ]);


        });

    }

};