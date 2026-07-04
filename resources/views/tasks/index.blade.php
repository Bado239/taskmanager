<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Tâches d'Aujourd'hui</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .task-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container mt-4">
    <!-- DEUX BOUTONS TOUT EN HAUT -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('tasks.dashboard') }}" class="btn btn-outline-secondary px-4 fw-bold">📊 Dashboard</a>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary px-4 fw-bold">➕ Nouvelle tâche</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- BLOC UNIQUE : AUJOURD'HUI -->
    <div class="task-card">
        <h2 class="text-primary fw-bold mb-4">📅 Aujourd'hui</h2>
        @include('tasks.partials.table', ['tasks' => $todayTasks])
    </div>
</div>

<!-- 🌟 PIED DE PAGE & SIGNATURE BADO 🌟 -->
<footer class="text-center py-4 mt-5" style="background: linear-gradient(180deg, rgba(255,255,255,0) 0%, #ffffff 100%); border-top: 1px dashed #dee2e6;">
    <div class="container">
        <div class="d-flex flex-column align-items-center justify-content-center gap-1">
            <!-- Ligne principale -->
            <p class="mb-0 fw-semibold text-secondary" style="letter-spacing: 0.8px; font-size: 0.95rem;">
                🚀 <span class="text-dark border-end pe-2 me-2">TaskManager</span> 
                Propulsé avec passion par 
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold ms-1 shadow-sm" style="letter-spacing: 1px;">
                    ✍️ BADO
                </span>
            </p>
            <!-- Ligne copyright secondaire -->
            <small class="text-muted opacity-75" style="font-size: 0.75rem;">
                &copy; {{ date('Y') }} &bull; Tous droits réservés &bull; Amélioration continue
            </small>
        </div>
    </div>
</footer>

</body>
</html>