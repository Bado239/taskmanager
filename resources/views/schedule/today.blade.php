
@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-7xl space-y-4 pb-12">

    {{-- EN-TÊTE --}}
    <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-3">

            <div>
                <h1 class="text-xl font-extrabold text-slate-900">
                    🗓️ Emploi du temps global
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Programme du {{ $today->format('d/m/Y') }}
                    — Office et Master
                </p>
            </div>

            <a
                href="{{ route('dashboard', ['view' => 'dashboard']) }}"
                class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100"
            >
                ← Retour au Dashboard
            </a>

        </div>

    </section>

    {{-- INDICATEURS --}}
    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">

        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Total des tâches du jour
            </p>
            <p class="mt-2 text-2xl font-extrabold text-blue-700">
                {{ $todayTasks->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Mode Office
            </p>
            <p class="mt-2 text-2xl font-extrabold text-indigo-700">
                {{ $officeCount }}
            </p>
        </div>

        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Mode Master
            </p>
            <p class="mt-2 text-2xl font-extrabold text-purple-700">
                {{ $masterCount }}
            </p>
        </div>

        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Tâches terminées
            </p>
            <p class="mt-2 text-2xl font-extrabold text-green-700">
                {{ $completedCount }}
            </p>
        </div>

    </div>

    
    {{-- MESSAGES DE CRÉATION --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <p class="font-bold">Veuillez corriger les erreurs :</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULAIRE DE CRÉATION --}}
    <section
        id="scheduleTaskFormContainer"
        class="{{ $errors->any() ? '' : 'hidden' }} rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm"
    >
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-base font-extrabold text-slate-900">
                ➕ Programmer une tâche aujourd'hui
            </h2>

            <button
                type="button"
                onclick="toggleScheduleTaskForm(false)"
                class="text-xs font-bold text-slate-500 hover:text-red-600"
            >
                ✕ Fermer
            </button>
        </div>

        <form
            action="{{ route('tasks.store') }}"
            method="POST"
            class="grid grid-cols-1 gap-4 md:grid-cols-2"
        >
            @csrf

            {{-- Origine du formulaire --}}
            <input type="hidden" name="return_to" value="schedule.today">

            {{-- Date d'exécution : aujourd'hui --}}
            <input
                type="hidden"
                name="execution_date"
                value="{{ $today->format('Y-m-d') }}"
            >

            <input
                type="hidden"
                name="date_prevue"
                value="{{ $today->format('Y-m-d') }}"
            >

            {{-- MODE --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    1. Mode *
                </label>

                <select
                    name="type"
                    id="scheduleTaskMode"
                    required
                    onchange="filterScheduleTaskOptions()"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                >
                    <option value="">-- Choisir un mode --</option>
                    <option value="office" @selected(old('type') === 'office')>
                        💼 Mode Office
                    </option>
                    <option value="master" @selected(old('type') === 'master')>
                        🎓 Mode Master
                    </option>
                </select>
            </div>

            {{-- PROJET OU MATIÈRE --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    2. Projet / Matière *
                </label>

                <select
                    name="project_id"
                    id="scheduleTaskProject"
                    required
                    data-old-value="{{ old('project_id') }}"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                >
                    <option value="">-- Choisir un projet ou une matière --</option>

                    @foreach($scheduleProjects as $project)
                        <option
                            value="{{ $project->id }}"
                            data-mode="{{ $project->type }}"
                        >
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ÉTAPE OU LEÇON --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    3. Étape / Leçon
                </label>

                <select
                    name="category_id"
                    id="scheduleTaskCategory"
                    data-old-value="{{ old('category_id') }}"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                >
                    <option value="">-- Facultatif --</option>

                    @foreach($scheduleCategories as $category)
                        <option
                            value="{{ $category->id }}"
                            data-mode="{{ $category->type }}"
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DATE --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Date d'exécution
                </label>

                <input
                    type="text"
                    readonly
                    value="{{ $today->format('d/m/Y') }}"
                    class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-bold text-slate-700"
                >
            </div>

            {{-- LIBELLÉ --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    4. Libellé de la tâche *
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    maxlength="255"
                    placeholder="Ex : Réviser le chapitre 3"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none"
                >
            </div>

            {{-- HEURE DÉBUT --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    5. Heure de début *
                </label>

                <input
                    type="time"
                    name="start_time"
                    value="{{ old('start_time', '09:00') }}"
                    required
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                >
            </div>

            {{-- HEURE FIN --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    6. Heure de fin *
                </label>

                <input
                    type="time"
                    name="end_time"
                    value="{{ old('end_time', '11:00') }}"
                    required
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                >
            </div>

            {{-- STATUT --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    7. Statut
                </label>

                <select
                    name="document_status"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                >
                    <option value="todo" @selected(old('document_status', 'todo') === 'todo')>
                        À faire
                    </option>
                    <option value="in_progress" @selected(old('document_status') === 'in_progress')>
                        En cours
                    </option>
                    <option value="done" @selected(old('document_status') === 'done')>
                        Terminée
                    </option>
                </select>
            </div>

            {{-- PRIORITÉ --}}
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    8. Priorité
                </label>

                <select
                    name="priority"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                >
                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>
                        Moyenne
                    </option>
                    <option value="high" @selected(old('priority') === 'high')>
                        Haute
                    </option>
                    <option value="low" @selected(old('priority') === 'low')>
                        Basse
                    </option>
                </select>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 md:col-span-2">

                <button
                    type="button"
                    onclick="toggleScheduleTaskForm(false)"
                    class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100"
                >
                    Annuler
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-xs font-bold text-white hover:bg-blue-700"
                >
                    💾 Enregistrer la tâche
                </button>

            </div>
        </form>
    </section>


    {{-- TABLEAU DES TÂCHES --}}
    <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">


            <div class="flex flex-wrap items-center justify-between gap-3">

                <h2 class="text-base font-extrabold text-slate-900">
                    📋 Programme de la journée
                </h2>

                <button
                    type="button"
                    onclick="toggleScheduleTaskForm()"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-700"
                >
                    + Nouvelle tâche
                </button>

            </div>

            <p class="mt-1 text-xs text-slate-500">
                Les tâches sont classées par heure de début.
                Les tâches terminées sont placées en dernier.
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1050px] text-left text-xs">

                <thead class="border-b border-blue-100 bg-blue-50">

                    <tr class="font-extrabold uppercase text-slate-600">
                        <th class="px-4 py-3">Horaires</th>
                        <th class="px-4 py-3">Mode</th>
                        <th class="px-4 py-3">Projet / Matière</th>
                        <th class="px-4 py-3">Étape / Leçon</th>
                        <th class="px-4 py-3">Tâche</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Priorité</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($todayTasks as $task)

                        <tr class="transition hover:bg-blue-50/50">

                            {{-- HORAIRES --}}
                            <td class="whitespace-nowrap px-4 py-4 font-semibold text-slate-700">

                                {{ $task->heure_debut
                                    ? substr((string) $task->heure_debut, 0, 5)
                                    : '--:--' }}

                                à

                                {{ $task->heure_fin
                                    ? substr((string) $task->heure_fin, 0, 5)
                                    : '--:--' }}

                            </td>

                            {{-- MODE --}}
                            <td class="px-4 py-4">

                                @if($task->type === 'master')
                                    <span class="rounded-full bg-purple-50 px-3 py-1.5 font-bold text-purple-700">
                                        🎓 Master
                                    </span>
                                @else
                                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-700">
                                        💼 Office
                                    </span>
                                @endif

                            </td>

                            {{-- PROJET / MATIÈRE --}}
                            <td class="px-4 py-4 font-bold text-slate-800">
                                {{ $task->project->title
                                    ?? $task->project_name
                                    ?? 'Général' }}
                            </td>

                            {{-- ÉTAPE / LEÇON --}}
                            <td class="px-4 py-4 text-blue-600">
                                {{ $task->category->name ?? '—' }}
                            </td>

                            {{-- TÂCHE --}}
                            <td class="px-4 py-4 font-semibold text-slate-900">
                                {{ $task->title }}
                            </td>

                            {{-- STATUT --}}
                            <td class="px-4 py-4">

                                @if($task->document_status === 'done')

                                    <span class="rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-700">
                                        ✓ Terminée
                                    </span>

                                @elseif($task->document_status === 'in_progress')

                                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-700">
                                        ◷ En cours
                                    </span>

                                @else

                                    <span class="rounded-full bg-slate-100 px-3 py-1.5 font-bold text-slate-600">
                                        À faire
                                    </span>

                                @endif

                            </td>

                            {{-- PRIORITÉ --}}
                            <td class="px-4 py-4">

                                @if($task->priority === 'high')
                                    <span class="font-bold text-red-600">
                                        ↑ Haute
                                    </span>
                                @elseif($task->priority === 'low')
                                    <span class="font-bold text-green-700">
                                        ↓ Basse
                                    </span>
                                @else
                                    <span class="font-bold text-amber-700">
                                        = Moyenne
                                    </span>
                                @endif

                            </td>

                            {{-- ACTION --}}
                            <td class="px-4 py-4 text-right">

                                @if($task->type === 'master')

                                    <a
                                        href="{{ route('dashboard', [
                                            'view' => 'master',
                                            'folder' => $task->project_id
                                        ]) }}"
                                        class="inline-flex rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 font-bold text-blue-700 hover:bg-blue-100"
                                    >
                                        Ouvrir dossier
                                    </a>

                                @else

                                    <a
                                        href="{{ route('dashboard', ['view' => 'office']) }}"
                                        class="inline-flex rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 font-bold text-blue-700 hover:bg-blue-100"
                                    >
                                        Voir Office
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-500">

                                <p class="text-3xl">🗓️</p>

                                <p class="mt-2 font-bold text-slate-700">
                                    Aucune tâche prévue aujourd'hui.
                                </p>

                                <p class="mt-1">
                                    Les tâches Office et Master programmées
                                    pour cette journée apparaîtront ici.
                                </p>

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>

        </div>

    </section>

</div>


<script>
function toggleScheduleTaskForm(forceOpen = null) {
    const container = document.getElementById(
        'scheduleTaskFormContainer'
    );

    if (!container) return;

    const shouldOpen = forceOpen === null
        ? container.classList.contains('hidden')
        : forceOpen;

    container.classList.toggle('hidden', !shouldOpen);

    if (shouldOpen) {
        container.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

function filterScheduleTaskOptions() {
    const mode = document.getElementById('scheduleTaskMode').value;

    const project = document.getElementById('scheduleTaskProject');
    const category = document.getElementById('scheduleTaskCategory');

    [project, category].forEach(select => {
        Array.from(select.options).forEach(option => {
            if (!option.value) return;

            const allowed = option.dataset.mode === mode;

            option.disabled = !allowed;
            option.hidden = !allowed;
        });

        const selected = select.selectedOptions[0];

        if (selected && selected.disabled) {
            select.value = '';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const project = document.getElementById('scheduleTaskProject');
    const category = document.getElementById('scheduleTaskCategory');

    const oldProject = project.dataset.oldValue;
    const oldCategory = category.dataset.oldValue;

    filterScheduleTaskOptions();

    if (oldProject) {
        const option = Array.from(project.options)
            .find(item => item.value === oldProject && !item.disabled);

        if (option) project.value = oldProject;
    }

    if (oldCategory) {
        const option = Array.from(category.options)
            .find(item => item.value === oldCategory && !item.disabled);

        if (option) category.value = oldCategory;
    }
});
</script>


@endsection
