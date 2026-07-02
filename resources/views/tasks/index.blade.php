<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Gestionnaire de Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .task-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0px 4px 10px rgba(0,0,0,0.03); margin-bottom: 25px; }
        .table-responsive { background: white; border-radius: 8px; overflow: hidden; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('tasks.index') }}">🎯 TaskManager</a>
        <div class="d-flex gap-2">
            <a href="{{ route('tasks.create') }}" class="btn btn-success btn-sm">➕ Ajouter une tâche</a>
        </div>
    </div>
</nav>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- 1. TÂCHES D'AUJOURD'HUI -->
    <div class="task-card">
        <h3 class="fw-bold text-primary mb-3">🎯 Aujourd'hui</h3>
        @include('tasks.partials.table', ['tasks' => $todayTasks])
    </div>

    <!-- 2. TÂCHES EN RETARD -->
    <div class="task-card">
        <h3 class="fw-bold text-danger mb-3">⚠️ En retard</h3>
        @include('tasks.partials.table', ['tasks' => $lateTasks])
    </div>

    <!-- 3. TÂCHES À VENIR -->
    <div class="task-card">
        <h3 class="fw-bold text-info mb-3">📅 À venir</h3>
        @include('tasks.partials.table', ['tasks' => $futureTasks])
    </div>

    <!-- 4. TÂCHES SANS DATE -->
    <div class="task-card">
        <h3 class="fw-bold text-secondary mb-3">📥 Boîte de réception (Sans date)</h3>
        @include('tasks.partials.table', ['tasks' => $noDateTasks])
    </div>
</div>

</body>
</html>