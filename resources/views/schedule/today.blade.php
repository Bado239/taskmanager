
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

    {{-- TABLEAU DES TÂCHES --}}
    <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">

            <h2 class="text-base font-extrabold text-slate-900">
                📋 Programme de la journée
            </h2>

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

@endsection
