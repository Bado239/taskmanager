<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .task-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container mt-4">
    <!-- DEUX BOUTONS DE NAVIGATION -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary px-4 fw-bold">📊 Dashboard</a>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary px-4 fw-bold">➕ Nouvelle tâche</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- SECTION 1 : AUJOURD'HUI -->
    <div class="task-card">
        <h2 class="text-primary fw-bold mb-4">📅 Aujourd'hui</h2>
        @include('tasks.partials.table', ['tasks' => $todayTasks])
    </div>

    <!-- SECTION 2 : EN RETARD -->
    <div class="task-card">
        <h2 class="text-danger fw-bold mb-4">⚠️ En retard</h2>
        @include('tasks.partials.table', ['tasks' => $lateTasks])
    </div>

    <!-- SECTION 3 : À VENIR -->
    <div class="task-card">
        <h2 class="text-success fw-bold mb-4">📆 À venir</h2>
        @include('tasks.partials.table', ['tasks' => $futureTasks])
    </div>

    <!-- SECTION 4 : SANS DATE -->
    <div class="task-card">
        <h2 class="text-secondary fw-bold mb-4">📥 Sans date</h2>
        @include('tasks.partials.table', ['tasks' => $noDateTasks])
    </div>
</div>

</body>
</html>