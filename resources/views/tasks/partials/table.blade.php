@if($tasks->count())
    <table class="table table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>Tâche</th>
                <th>Catégorie</th>
                <th>Projet</th>
                <th>Priorité</th>
                <th>Heure Début</th>
                <th>Heure Fin</th>
                <th width="160">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                @php
                    $isInProgress = false;
                    $now = \Carbon\Carbon::now('Africa/Dakar')->format('H:i:s');
                    $today = \Carbon\Carbon::today('Africa/Dakar')->format('Y-m-d');
                    
                    if ($task->date_prevue && \Carbon\Carbon::parse($task->date_prevue)->format('Y-m-d') === $today) {
                        if ($task->heure_debut && $task->heure_fin) {
                            $isInProgress = ($now >= $task->heure_debut && $now <= $task->heure_fin);
                        }
                    }
                @endphp

                <tr class="{{ $isInProgress ? 'table-warning fw-bold' : '' }}">
                    <td>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <strong>{{ $task->title }}</strong>
                            
                            <!-- Badge Statut Temporel -->
                            @if($isInProgress)
                                <span class="badge bg-warning text-dark">⚡ En cours</span>
                            @endif

                            <!-- 🔗 Bouton d'accès rapide au cours ou à l'emploi du temps -->
                            @if($task->document_link)
                                <a href="{{ $task->document_link }}" target="_blank" 
                                   class="badge bg-info text-dark text-decoration-none"
                                   title="Ouvrir le document de cours ou de planning">
                                    📄 Cours / Doc
                                </a>
                            @endif
                        </div>
                    </td>
                    <td>{{ $task->category->name ?? '-' }}</td>
                    <td>{{ $task->project->title ?? '-' }}</td>
                    <td>
                        @if($task->priority == 'high')
                            <span class="badge bg-danger">Haute</span>
                        @elseif($task->priority == 'medium')
                            <span class="badge bg-warning text-dark">Moyenne</span>
                        @else
                            <span class="badge bg-secondary">Basse</span>
                        @endif
                    </td>
                    <td>{{ $task->heure_debut ? \Carbon\Carbon::parse($task->heure_debut)->format('H:i') : '-' }}</td>
                    <td>{{ $task->heure_fin ? \Carbon\Carbon::parse($task->heure_fin)->format('H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm">✏️</a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette tâche ?')">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-light border text-center">
        Aucune tâche pour le moment.
    </div>
@endif