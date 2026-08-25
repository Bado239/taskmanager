<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Category;
use App\Models\PersonalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Mode actif
        $view = $request->get('view', 'dashboard');


        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES TÂCHES
        |--------------------------------------------------------------------------
        */

        $totalTasks = Task::count();

        $todoTasks = Task::where(
            'document_status',
            'todo'
        )->count();

        $doingTasks = Task::where(
            'document_status',
            'in_progress'
        )->count();

        $doneTasks = Task::where(
            'document_status',
            'done'
        )->count();


        $completionRate = $totalTasks > 0
            ? round(($doneTasks / $totalTasks) * 100)
            : 0;



        /*
        |--------------------------------------------------------------------------
        | MODES OFFICE / MASTER
        |--------------------------------------------------------------------------
        */

        $officeTasks = Task::with([
                'project',
                'category'
            ])
            ->where('type', 'office')
            ->orderByDesc('created_at')
            ->get();



        $masterTasks = Task::with([
                'project',
                'category'
            ])
            ->where('type', 'master')
            ->orderByDesc('created_at')
            ->get();



        /*
        |--------------------------------------------------------------------------
        | TOUTES LES TÂCHES
        |--------------------------------------------------------------------------
        */

        $allTasks = Task::with([
                'project',
                'category'
            ])
            ->orderByDesc('created_at')
            ->get();



        /*
        |--------------------------------------------------------------------------
        | 🌱 DEVELOPPEMENT PERSONNEL
        |--------------------------------------------------------------------------
        */

        $personalResources = collect();


        if ($view === 'personal') {


            $personalResources = PersonalResource::query()

                ->where(
                    'is_active',
                    DB::raw('true')
                )

                ->orderByDesc(
                    'created_at'
                )

                ->get();

        }




        /*
        |--------------------------------------------------------------------------
        | JOURNÉE
        |--------------------------------------------------------------------------
        */

        $tasksToday = Task::with([
                'project',
                'category'
            ])
            ->whereDate(
                'date_prevue',
                $today
            )
            ->orderBy(
                'ordre'
            )
            ->orderBy(
                'heure_debut'
            )
            ->get();



        $urgentTasks = Task::where(
                'priority',
                'high'
            )
            ->where(
                'document_status',
                '!=',
                'done'
            )
            ->count();



        $tasksWeek = Task::whereBetween(
                'date_prevue',
                [
                    $today,
                    $today->copy()->addDays(7)
                ]
            )
            ->count();



        $totalMinutesToday = Task::whereDate(
                'date_prevue',
                $today
            )
            ->sum(
                'duree_estimee'
            );




        /*
        |--------------------------------------------------------------------------
        | DONNEES FORMULAIRES
        |--------------------------------------------------------------------------
        */

        $categories = Category::all();

        $projects = Project::all();



        return view(
            'dashboard',
            compact(

                'view',

                'totalTasks',
                'todoTasks',
                'doingTasks',
                'doneTasks',

                'completionRate',

                'allTasks',

                'officeTasks',

                'masterTasks',

                'personalResources',

                'tasksToday',

                'urgentTasks',

                'tasksWeek',

                'totalMinutesToday',

                'categories',

                'projects'

            )
        );
    }
}