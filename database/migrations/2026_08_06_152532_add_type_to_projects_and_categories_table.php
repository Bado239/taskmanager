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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('type')->default('office')->after('title'); // 'office' ou 'master'
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('type')->default('office')->after('title'); // 'office' ou 'master'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
