<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Focus Journée - TaskManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .focus-container { background: white; border-radius: 15px; padding: 30px; box-shadow: 0px 4px 15px rgba(0,0,0,0.05); }
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
        <p class="text-muted mb-0">Planification et priorités d'aujourd'hui</p>
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
                    <th scope="col" class="py-3">Priorité</th>
                    <th scope="col" class="py-3" style="width: 20%;">Progression</th>
                    <th scope="col" class="py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayTasks as $task)
                    <tr>
                        <td class="fw-bold">{{ $task->title }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $task->category->name ?? '-' }}</span></td>
                        <td><span class="text-secondary">{{ $task->project->title ?? '-' }}</span></td>
                        <td>
                            @if($task->priority === 'high')
                                <span class="badge bg-danger">Haute</span>
                            @elseif($task->priority === 'medium')
                                <span class="badge bg-warning text-dark">Moyenne</span>
                            @else
                                <span class="badge bg-secondary">Basse</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress w-100" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $task->progress }}%;" aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted fw-bold">{{ $task->progress }}%</small>
                            </div>
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
                            🎉 Aucune activité programmée pour aujourd'hui !
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>