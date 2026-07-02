<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crée des catégories de test
        Category::firstOrCreate(['name' => 'Professionnel']);
        Category::firstOrCreate(['name' => 'Personnel']);
        Category::firstOrCreate(['name' => 'Études']);

        // Crée des projets de test
        Project::firstOrCreate(['title' => 'Développement Web']);
        Project::firstOrCreate(['title' => 'Organisation']);
    }
}