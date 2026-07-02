<div class="table-responsive border rounded">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col" class="py-3 ps-3">Titre</th>
                <th scope="col" class="py-3">Catégorie</th>
                <th scope="col" class="py-3">Projet</th>
                <th scope="col" class="py-3">Priorité</th>
                <th scope="col" class="py-3" style="width: 25%;">Progression</th>
                <th scope="col" class="py-3 text-end pe-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td class="fw-bold ps-3">{{ $task->title }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $task->category->name ?? 'Aucune' }}
                        </span>
                    </td>
                    <td>
                        <span class="text-secondary">
                            {{ $task->project->title ?? 'Aucun' }}
                        </span>
                    </td>
                    <td>
                        @if(($task->priority ?? '') === 'high')
                            <span class="badge bg-danger">Haute</span>
                        @elseif(($task->priority ?? '') === 'medium')
                            <span class="badge bg-warning text-dark">Moyenne</span>
                        @else
                            <span class="badge bg-secondary">Basse</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress w-100" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;"></div>
                            </div>
                            <small class="text-muted fw-bold">{{ $task->progress ?? 0 }}%</small>
                        </div>
                    </td>
                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">
                            <!-- BOUTON MODIFIER -->
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-warning" title="Modifier">✏️</a>
                            
                            <!-- BOUTON SUPPRIMER -->
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Aucune tâche prévue pour aujourd'hui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>