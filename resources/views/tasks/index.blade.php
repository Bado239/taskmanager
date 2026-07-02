<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des tâches</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            background:#f4f6f9;
        }

        .task-card{
            background:white;
            border-radius:12px;
            padding:20px;
            margin-bottom:30px;
            box-shadow:0 2px 10px rgba(0,0,0,.08);
        }

    </style>

</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>📋 Gestion des tâches</h2>

        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            Nouvelle tâche
        </a>

    </div>

    @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

    @endif


    <!-- AUJOURD'HUI -->

    <div class="task-card">

        <h3 class="text-primary fw-bold mb-3">

            📅 Aujourd'hui

        </h3>

        @include('tasks.partials.table',['tasks'=>$todayTasks])

    </div>


    <!-- EN RETARD -->

    <div class="task-card">

        <h3 class="text-danger fw-bold mb-3">

            ⚠️ En retard

        </h3>

        @include('tasks.partials.table',['tasks'=>$lateTasks])

    </div>


    <!-- A VENIR -->

    <div class="task-card">

        <h3 class="text-success fw-bold mb-3">

            📆 À venir

        </h3>

        @include('tasks.partials.table',['tasks'=>$futureTasks])

    </div>


    <!-- SANS DATE -->

    <div class="task-card">

        <h3 class="text-secondary fw-bold mb-3">

            📥 Sans date

        </h3>

        @include('tasks.partials.table',['tasks'=>$noDateTasks])

    </div>


</div>

</body>

</html>