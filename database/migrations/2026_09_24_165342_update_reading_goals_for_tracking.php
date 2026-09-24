<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('reading_goals', function (Blueprint $table) {


            $table->integer('daily_pages')
                ->default(10)
                ->after('start_page');


            $table->integer('delay_pages')
                ->default(0)
                ->after('daily_pages');


            $table->integer('calculation_start_page')
                ->default(1)
                ->after('delay_pages');


        });
    }



    public function down()
    {
        Schema::table('reading_goals', function (Blueprint $table) {

            $table->dropColumn([
                'daily_pages',
                'delay_pages',
                'calculation_start_page'
            ]);

        });
    }

};
