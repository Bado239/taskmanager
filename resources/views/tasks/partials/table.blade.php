@if($tasks->count())

<table class="table table-hover align-middle">

    <thead class="table-dark">

        <tr>

            <th>Tâche</th>

            <th>Catégorie</th>

            <th>Projet</th>

            <th>Priorité</th>

            <th>Date prévue</th>

            <th>Progression</th>

            <th width="160">Actions</th>

        </tr>

    </thead>

    <tbody>

    @foreach($tasks as $task)

        <tr>

            <td>

                <strong>{{ $task->title }}</strong>

            </td>

            <td>

                {{ $task->category->name ?? '-' }}

            </td>

            <td>

                {{ $task->project->title ?? '-' }}

            </td>

            <td>

                @if($task->priority=='high')

                    <span class="badge bg-danger">
                        Haute
                    </span>

                @elseif($task->priority=='medium')

                    <span class="badge bg-warning text-dark">
                        Moyenne
                    </span>

                @else

                    <span class="badge bg-secondary">
                        Basse
                    </span>

                @endif

            </td>

            <td>

                {{ $task->date_prevue ?? '-' }}

            </td>

            <td width="180">

                <div class="progress">

                    <div class="progress-bar
                        @if($task->progress==100)
                            bg-success
                        @elseif($task->progress>0)
                            bg-warning
                        @else
                            bg-secondary
                        @endif"
                        role="progressbar"
                        style="width: {{ $task->progress }}%;">

                        {{ $task->progress }}%

                    </div>

                </div>

            </td>

            <td>

                <a href="{{ route('tasks.edit',$task->id) }}"
                   class="btn btn-warning btn-sm">

                    ✏️

                </a>

                <form action="{{ route('tasks.destroy',$task->id) }}"
                      method="POST"
                      style="display:inline;">

                    @csrf
                    @method('DELETE')

                    <button
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Supprimer cette tâche ?')">

                        🗑️

                    </button>

                </form>

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

@else

<div class="alert alert-light border text-center">

    Aucune tâche.

</div>

@endif