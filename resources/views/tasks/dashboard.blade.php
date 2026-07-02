<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord - TaskManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0px 4px 10px rgba(0,0,0,0.03); text-decoration: none; color: inherit; display: block; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-3px); color: inherit; }
        .list-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0px 4px 15px rgba(0,0,0,0.05); }
        .row-in-progress { font-weight: bold; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('tasks.index') }}">📊 Tableau de Bord</a>
        <a href="{{ route('tasks.index') }}" class="btn btn-light btn-sm">🎯 Retour au Focus Journée</a>
    </div>
</nav>

<div class="container">
    <h2 class="fw-bold text-dark mb-4">📊 Indicateurs de Performance</h2>

    <!-- CARTES DES INDICATEURS -->
    <div class="row g-3 mb-4">
        <!-- 1. Passées / En retard -->
        <div class="col-md-4">
            <a href="{{ route('tasks.dashboard', ['filter' => 'late']) }}" class="stat-card border-start border-danger border-4">
                <span class="text-muted small text-uppercase fw-bold">Passées / En retard</span>
                <h2 class="fw-bold text-danger m-0">{{ $countLate }}</h2>
            </a>
        </div>
        <!-- 2. Activités à venir -->
        <div class="col-md-4">
            <a href="{{ route('tasks.dashboard', ['filter' => 'future']) }}" class="stat-card border-start border-primary border-4">
                <span class="text-muted small text-uppercase fw-bold">Activités à venir</span>
                <h2 class="fw-bold text-primary m-0">{{ $countFuture }}</h2>
            </a>
        </div>
        <!-- 3. Activités sans date -->
        <div class="col-md-4">
            <a href="{{ route('tasks.dashboard', ['filter' => 'nodate']) }}" class="stat-card border-start border-secondary border-4">
                <span class="text-muted small text-uppercase fw-bold">Sans date de planification</span>
                <h2 class="fw-bold text-secondary m-0">{{ $countNoDate }}</h2>
            </a>
        </div>
    </div>

    <!-- LISTE DYNAMIQUE DES TÂCHES FILTRÉES -->
    @if($filter)
        <div class="list-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold m-0 text-capitalize">
                    @if($filter === 'late') 🔴 Liste des activités passées / en retard
                    @elseif($filter === 'future') 🔵 Liste des activités à venir
                    @elseif($filter === 'nodate') ⚫ Liste des activités de fond (sans date)
                    @endif
                </h4>
                <a href="{{ route('tasks.dashboard') }}" class="btn-close" aria-label="Close"></a>
            </div>

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Projet</th>
                        @if($filter !== 'nodate')
                            <th>Date prévue</th>
                        @endif
                        <th>Heure début</th>
                        <th>Heure fin</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        @php
                            $isInProgress = false;
                            $today = \Illuminate\Support\Carbon::today()->format('Y-m-d');
                            // Si c'est aujourd'hui et qu'on est dans le créneau horaire
                            if ($task->date_prevue && \Illuminate\Support\Carbon::parse($task->date_prevue)->format('Y-m-d') === $today) {
                                if ($task->heure_debut && $task->heure_fin) {
                                    $isInProgress = ($filter === 'late' || $now >= $task->heure_debut && $now <= $task->heure_fin);
                                }
                            }
                        @endphp

                        <tr class="{{ $isInProgress ? 'table-warning row-in-progress' : '' }}">
                            <td>{{ $task->title }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $task->category->name ?? '-' }}</span></td>
                            <td><span class="text-secondary">{{ $task->project->title ?? '-' }}</span></td>
                            @if($filter !== 'nodate')
                                <td>{{ $task->date_prevue ? \Carbon\Carbon::parse($task->date_prevue)->format('d/m/Y') : '-' }}</td>
                            @endif
                            <td>{{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i') : '-' }}</td>
                            <td>
                                {{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '-' }}
                                @if($isInProgress && $filter !== 'late')
                                    <span class="badge bg-warning text-dark ms-2">⚡ En cours</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-primary">✏️</a>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucune activité trouvée dans cette catégorie.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center py-4">
            💡 Cliquez sur un des indicateurs ci-dessus pour afficher la liste des activités correspondantes.
        </div>
    @endif
</div>

</body>
</html>