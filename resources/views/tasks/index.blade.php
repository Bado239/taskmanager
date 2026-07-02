<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Focus Journée - TaskManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .focus-container { background: white; border-radius: 15px; padding: 30px; box-shadow: 0px 4px 15px rgba(0,0,0,0.05); }
        .row-in-progress { font-weight: bold; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('tasks.index') }}">🎯 Focus Journée</a>
        <div class="d-flex gap-2">
            <a href="{{ route('tasks.create') }}" class="btn btn-success btn-sm">➕ Ajouter une tâche</a>
            <a href="{{ route('tasks.dashboard') }}" class="btn btn-light btn-sm">📊 Aller au Tableau de Bord</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-0">🎯 Mes activités du jour</h2>
        <p class="text-muted mb-0">Heure actuelle : <strong>{{ \Carbon\Carbon::parse($now)->format('H:i') }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="focus-container">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col" class="py-3">Titre</th>
                    <th scope="col" class="py-3">Catégorie</th>
                    <th scope="col" class="py-3">Projet</th>
                    <th scope="col" class="py-3">Heure début</th>
                    <th scope="col" class="py-3">Heure fin</th>
                    <th scope="col" class="py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayTasks as $task)
                    @php
                        // Détermination dynamique si la tâche est en cours actuellement
                        $isInProgress = false;
                        if ($task->heure_debut && $task->heure_fin) {
                            $isInProgress = ($now >= $task->heure_debut && $now <= $task->heure_fin);
                        }
                    @endphp
                    
                    <tr class="{{ $isInProgress ? 'table-warning row-in-progress' : '' }}">
                        <td>{{ $task->title }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $task->category->name ?? '-' }}</span></td>
                        <td><span class="text-secondary">{{ $task->project->title ?? '-' }}</span></td>
                        <td>
                            {{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i') : '-' }}
                        </td>
                        <td>
                            {{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '-' }}
                            @if($isInProgress)
                                <span class="badge bg-warning text-dark ms-2">⚡ En cours</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-primary">✏️</a>
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Supprimer cette tâche ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            🎉 Aucune activité restante programmée pour aujourd'hui !
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>