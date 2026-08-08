<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

return new class extends Migration {
    public function up(): void
    {
        // 1. S'assure que la colonne 'type' existe dans la table 'categories'
        if (!Schema::hasColumn('categories', 'type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('type')->default('office')->after('name');
            });
        }

        // 2. Assigne par défaut le type 'office' à toutes les catégories existantes qui n'en ont pas
        Category::whereNull('type')->orWhere('type', '')->update(['type' => 'office']);
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};