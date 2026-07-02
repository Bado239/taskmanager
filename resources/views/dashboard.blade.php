<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - TaskManager Pro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .card-kpi {
            border: none;
            border-radius: 15px;
            box-shadow: 0px 3px 12px rgba(0,0,0,0.1);
        }

        .header-bar {
            background: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.08);
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- HEADER -->
    <div class="header-bar d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-0">📊 TaskManager Pro Dashboard</h3>
            <small class="text-muted">Suivi global des activités et performances</small>
        </div>

        <div>
            <a href="/" class="btn btn-dark btn-sm">🔙 Accueil</a>
            <a href="/tasks" class="btn btn-primary btn-sm">📋 Tâches</a>
            <a href="/tasks/create" class="btn btn-success btn-sm">➕ Ajouter</a>
        </div>

    </div>

    <!-- KPI PRINCIPAUX -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card card-kpi bg-primary text-white p-3">
                <h6>📌 Total tâches</h6>
                <h2>{{ $totalTasks }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-kpi bg-warning text-dark p-3">
                <h6>🕒 À faire</h6>
                <h2>{{ $todoTasks }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-kpi bg-info text-white p-3">
                <h6>⚙️ En cours</h6>
                <h2>{{ $doingTasks }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-kpi bg-success text-white p-3">
                <h6>✅ Terminées</h6>
                <h2>{{ $doneTasks }}</h2>
            </div>
        </div>

    </div>

    <!-- ANALYSE -->
    <div class="row mt-4 g-3">

        <!-- TÂCHES PRIORITAIRES -->
        <div class="col-md-6">
            <div class="card p-3">
                <h5>🔥 Tâches prioritaires</h5>

                <p class="text-muted">
                    Ensemble des tâches actives ou en attente de traitement
                </p>

                <h3>{{ $todoTasks + $doingTasks }}</h3>
            </div>
        </div>

        <!-- AVANCEMENT GLOBAL -->
        <div class="col-md-6">
            <div class="card p-3">
                <h5>📈 Taux d’avancement global</h5>

                @php
                    $total = $totalTasks;
                    $rate = $total > 0 ? round(($doneTasks / $total) * 100) : 0;
                @endphp

                <h3>{{ $rate }}%</h3>

                <div class="progress">
                    <div class="progress-bar bg-success"
                         style="width: {{ $rate }}%;">
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- ALERTES -->
    <div class="row mt-4">

        <div class="col-md-6">
            <div class="card p-3 border-danger">
                <h5>⚠️ Tâches critiques</h5>
                <p class="text-muted">
                    Tâches en attente nécessitant une action
                </p>

                <h3>{{ $todoTasks }}</h3>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 border-success">
                <h5>📊 Performance globale</h5>
                <p class="text-muted">
                    Niveau d’exécution du système
                </p>

                <h3>{{ $doneTasks }}/{{ $totalTasks }}</h3>
            </div>
        </div>

    </div>

</div>

</body>
</html>