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

            $table->integer('pages_per_day')
                ->default(10)
                ->after('target_page');

        });
    }



    public function down()
    {
        Schema::table('reading_goals', function (Blueprint $table) {

            $table->dropColumn('pages_per_day');

        });
    }

};
