<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('study_raid_sources', function (Blueprint $table) {

            $table->id();


            $table->foreignId('task_id')
                ->constrained()
                ->cascadeOnDelete();


            $table->text('url');


            $table->timestamps();

        });

    }



    public function down(): void
    {

        Schema::dropIfExists('study_raid_sources');

    }

};