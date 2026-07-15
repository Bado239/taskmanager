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
    Schema::table('tasks', function (Blueprint $table) {
        // Distinguer le Master du Bureau
        $table->string('type')->default('office'); // 'master' ou 'office'
        
        // Suivi de l'état d'un document (livrable ou cours)
        $table->string('document_status')->default('none'); // 'none', 'todo', 'in_progress', 'done'
    });
}

public function down()
{
    Schema::table('tasks', function (Schema $table) {
        $table->dropColumn(['type', 'document_status']);
    });
}
};
