
@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-7xl space-y-5 pb-12">

    @php
        $dayNames = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];

        $days = collect();

        for ($i = 0; $i < 7; $i++) {
            $days->push($weekStart->copy()->addDays($i));
        }

        $progressRate = $completionRate;

        $statusLabels = [
            'todo' => 'À faire',
            'in_progress' => 'En cours',
            'done' => 'Terminée',
        ];

        $statusColors = [
            'todo' => 'bg-amber-50 text-amber-700 border-amber-200',
            'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
            'done' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        ];
    @endphp

    {{-- EN-TÊTE --}}
    <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-wider text-blue-600">
                    Organisation personnelle
                </p>

                <h1 class="text-xl font-extrabold text-slate-900 sm:text-2xl">
                    🗓️ Emploi du temps hebdomadaire
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Semaine du
                    {{ $weekStart->format('d/m/Y') }}
                    au
                    {{ $weekEnd->format('d/m/Y') }}
                    · Office et Master
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('schedule.today') }}"
                    class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100"
                >
                    📅 Aujourd'hui
                </a>

                <a
                    href="{{ route('dashboard', ['view' => 'dashboard']) }}"
                    class="rounded-xl bg-slate-800 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-900"
                >
                    ← Dashboard
                </a>

            </div>

        </div>

    </section>


    {{-- INDICATEURS HEBDOMADAIRES --}}
    <section class="grid grid-cols-2 gap-3 md:grid-cols-4">

        <div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Total des tâches
            </p>

            <p class="mt-2 text-3xl font-extrabold text-blue-700">
                {{ $totalTasks }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Cette semaine
            </p>
        </div>

        <div class="rounded-2xl border border-emerald-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Terminées
            </p>

            <p class="mt-2 text-3xl font-extrabold text-emerald-600">
                {{ $completedTasks }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Tâches réalisées
            </p>
        </div>

        <div class="rounded-2xl border border-amber-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                À faire
            </p>

            <p class="mt-2 text-3xl font-extrabold text-amber-600">
                {{ $pendingTasks }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Tâches en attente
            </p>
        </div>

        <div class="rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                En cours
            </p>

            <p class="mt-2 text-3xl font-extrabold text-indigo-600">
                {{ $inProgressTasks }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Tâches en progression
            </p>
        </div>

    </section>


    {{-- TAUX DE RÉALISATION --}}
    <section class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">

        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">

            <div>
                <h2 class="text-sm font-extrabold text-slate-900">
                    🎯 Performance hebdomadaire
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Progression des tâches programmées cette semaine
                </p>
            </div>

            <span class="text-2xl font-extrabold text-emerald-600">
                {{ $progressRate }} %
            </span>

        </div>

        <div class="h-3 overflow-hidden rounded-full bg-slate-100">

            <div
                class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                style="width: {{ $progressRate }}%"
            ></div>

        </div>

        <p class="mt-2 text-xs text-slate-500">
            {{ $completedTasks }} tâche(s) terminée(s)
            sur {{ $totalTasks }} tâche(s) programmée(s).
        </p>

    </section>


    {{-- RÉPARTITION OFFICE / MASTER --}}
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2">

        <div class="flex items-center justify-between rounded-2xl border border-indigo-100 bg-white p-4 shadow-sm">

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">
                    💼 Mode Office
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ $weekTasks->where('type', 'office')->count() }}
                </p>

                <p class="text-xs text-slate-500">
                    Tâches professionnelles
                </p>
            </div>

            <span class="rounded-xl bg-indigo-50 p-3 text-2xl">
                💼
            </span>

        </div>

        <div class="flex items-center justify-between rounded-2xl border border-purple-100 bg-white p-4 shadow-sm">

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-purple-600">
                    🎓 Mode Master
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ $weekTasks->where('type', 'master')->count() }}
                </p>

                <p class="text-xs text-slate-500">
                    Tâches académiques
                </p>
            </div>

            <span class="rounded-xl bg-purple-50 p-3 text-2xl">
                🎓
            </span>

        </div>

    </section>


    {{-- PROGRAMME DES SEPT JOURS --}}
    <section class="space-y-4">

        <div class="flex flex-wrap items-center justify-between gap-3">

            <div>
                <h2 class="text-lg font-extrabold text-slate-900">
                    📋 Programme de la semaine
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Consultez l'ensemble de vos activités quotidiennes.
                </p>
            </div>

            <span class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700">
                7 jours
            </span>

        </div>


        @foreach($days as $day)

            @php
                $dateKey = $day->toDateString();

                $dayTasks = $tasksByDay->get($dateKey, collect());

                $isToday = $now->isSameDay($day);

                $dayDone = $dayTasks
                    ->where('document_status', 'done')
                    ->count();

                $dayTotal = $dayTasks->count();

                $dayRate = $dayTotal > 0
                    ? round($dayDone * 100 / $dayTotal)
                    : 0;

                $orderedTasks = $dayTasks
                    ->sortBy(function ($task) {
                        $group = $task->document_status === 'done' ? 1 : 0;

                        $time = $task->heure_debut
                            ? substr((string) $task->heure_debut, 0, 5)
                            : '99:99';

                        return $group . '|' . $time . '|' . $task->id;
                    })
                    ->values();
            @endphp


            <article
                class="overflow-hidden rounded-2xl border bg-white shadow-sm
                {{ $isToday ? 'border-blue-300 ring-1 ring-blue-100' : 'border-slate-200' }}"
            >

                {{-- EN-TÊTE DU JOUR --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-4 py-4
                    {{ $isToday ? 'bg-blue-50' : 'bg-slate-50' }}">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl
                            {{ $isToday ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 border border-slate-200' }}">

                            <span class="text-lg font-extrabold">
                                {{ $day->format('d') }}
                            </span>

                        </div>

                        <div>

                            <h3 class="text-sm font-extrabold text-slate-900">

                                {{ $dayNames[$day->dayOfWeekIso] }}

                                @if($isToday)
                                    <span class="ml-1 text-xs font-bold text-blue-600">
                                        · AUJOURD'HUI
                                    </span>
                                @endif

                            </h3>

                            <p class="text-xs text-slate-500">
                                {{ $day->format('d/m/Y') }}
                            </p>

                        </div>

                    </div>


                    <div class="flex items-center gap-3">

                        <div class="text-right">

                            <p class="text-sm font-extrabold text-slate-800">
                                {{ $dayTotal }} tâche(s)
                            </p>

                            <p class="text-xs text-emerald-600">
                                {{ $dayDone }} terminée(s)
                            </p>

                        </div>

                        @if($dayTotal > 0)

                            <div class="flex h-11 w-11 items-center justify-center rounded-full border-4 border-emerald-100 text-xs font-extrabold text-emerald-700">

                                {{ $dayRate }}%

                            </div>

                        @else

                            <span class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-400">
                                Libre
                            </span>

                        @endif

                    </div>

                </div>


                {{-- LISTE DES TÂCHES --}}
                <div class="divide-y divide-slate-100">

                    @forelse($orderedTasks as $task)

                        @php
                            $status = $task->document_status ?? 'todo';

                            $statusColor = $statusColors[$status]
                                ?? 'bg-slate-50 text-slate-600 border-slate-200';

                            $statusLabel = $statusLabels[$status]
                                ?? 'Non défini';

                            $priorityColors = [
                                'high' => 'text-red-600 bg-red-50',
                                'medium' => 'text-amber-600 bg-amber-50',
                                'low' => 'text-slate-600 bg-slate-100',
                            ];

                            $priorityLabels = [
                                'high' => 'Haute',
                                'medium' => 'Moyenne',
                                'low' => 'Basse',
                            ];

                            $priorityColor = $priorityColors[$task->priority]
                                ?? 'text-slate-600 bg-slate-100';

                            $priorityLabel = $priorityLabels[$task->priority]
                                ?? 'Moyenne';
                        @endphp


                        <div class="flex flex-col gap-3 p-4 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">

                            {{-- INFORMATIONS DE LA TÂCHE --}}
                            <div class="min-w-0 flex-1">

                                <div class="mb-2 flex flex-wrap items-center gap-2">

                                    <span class="rounded-md px-2 py-1 text-[10px] font-extrabold uppercase
                                        {{ $task->type === 'master'
                                            ? 'bg-purple-50 text-purple-700'
                                            : 'bg-indigo-50 text-indigo-700' }}">

                                        {{ $task->type === 'master' ? '🎓 MASTER' : '💼 OFFICE' }}

                                    </span>

                                    <span class="rounded-md border px-2 py-1 text-[10px] font-bold {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>

                                    <span class="rounded-md px-2 py-1 text-[10px] font-bold {{ $priorityColor }}">
                                        Priorité {{ $priorityLabel }}
                                    </span>

                                </div>


                                <h4 class="break-words text-sm font-extrabold text-slate-900">
                                    {{ $task->title }}
                                </h4>

                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">

                                    <span>
                                        📁
                                        {{ $task->project->title
                                            ?? $task->project_name
                                            ?? 'Général' }}
                                    </span>

                                    @if($task->category)
                                        <span>
                                            📚 {{ $task->category->name }}
                                        </span>
                                    @endif

                                </div>

                            </div>


                            {{-- HORAIRES --}}
                            <div class="flex shrink-0 items-center gap-2">

                                <span class="rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-extrabold text-blue-700">

                                    🕒

                                    {{ $task->heure_debut
                                        ? substr((string) $task->heure_debut, 0, 5)
                                        : '--:--' }}

                                    -

                                    {{ $task->heure_fin
                                        ? substr((string) $task->heure_fin, 0, 5)
                                        : '--:--' }}

                                </span>

                            </div>

                        </div>

                    @empty

                        <div class="px-4 py-7 text-center">

                            <p class="text-2xl">☀️</p>

                            <p class="mt-2 text-sm font-bold text-slate-600">
                                Aucune tâche programmée
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Vous n'avez aucune activité enregistrée pour cette journée.
                            </p>

                        </div>

                    @endforelse

                </div>

            </article>

        @endforeach

    </section>


    {{-- PIED DE PAGE --}}
    <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-4 text-center">

        <p class="text-xs font-semibold text-blue-800">
            TaskManager Pro · Votre organisation, votre performance.
        </p>

        <p class="mt-1 text-xs text-blue-600">
            Planifiez intelligemment. Exécutez efficacement.
        </p>

    </div>

</div>

@endsection
