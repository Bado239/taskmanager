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
        Category::firstOrCreate(['name' => 'Master ISEF1']);
        Category::firstOrCreate(['name' => 'CGP']);

        // Crée des projets de test
        Project::firstOrCreate(['title' => 'VARQUAL']);
        Project::firstOrCreate(['title' => 'Calcul Stochastique']);
        Project::firstOrCreate(['title' => 'Finance']);
        Project::firstOrCreate(['title' => 'Etude 3FPT']);
        Project::firstOrCreate(['title' => 'Pauvreté']);
    }
}