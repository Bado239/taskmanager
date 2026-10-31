
@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-7xl space-y-5 pb-12">

    {{-- EN-TÊTE --}}
    <section class="rounded-2xl border border-blue-100 bg-white px-5 py-5 shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900">
                    🎓 Préparation aux examens
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Organisez vos examens et préparez vos révisions.
                </p>
            </div>

            <a
                href="{{ route('dashboard', ['view' => 'master']) }}"
                class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100"
            >
                ← Retour au Mode Master
            </a>
        </div>

    </section>

    {{-- MESSAGES --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-bold">Veuillez corriger les champs suivants :</p>

            <ul class="mt-2 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INDICATEURS --}}
    @php
        $today = \Carbon\Carbon::today();

        $upcomingExams = $exams->filter(
            fn ($exam) => $exam->exam_date->gte($today)
        );

        $nextExam = $upcomingExams->first();
    @endphp

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Examens enregistrés
            </p>
            <p class="mt-2 text-2xl font-extrabold text-blue-700">
                {{ $exams->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Examens à venir
            </p>
            <p class="mt-2 text-2xl font-extrabold text-slate-900">
                {{ $upcomingExams->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-blue-100 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">
                Prochain examen
            </p>
            <p class="mt-2 text-lg font-extrabold text-slate-900">
                {{ $nextExam
                    ? $nextExam->exam_date->format('d/m/Y')
                    : 'Non programmé' }}
            </p>
        </div>

    </div>

    {{-- FORMULAIRE --}}
    <section
        x-data="{ open: {{ $errors->any() ? 'true' : 'false' }} }"
        class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm"
    >

        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">

            <h2 class="text-base font-extrabold text-slate-900">
                📅 Mes examens
            </h2>

            <button
                type="button"
                @click="open = !open"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700"
            >
                + Nouvel examen
            </button>

        </div>

        <form
            x-show="open"
            x-cloak
            method="POST"
            action="{{ route('exams.store') }}"
            class="grid grid-cols-1 gap-4 border-b border-blue-100 bg-slate-50 p-5 md:grid-cols-2"
        >
            @csrf

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Matière *
                </label>

                <select
                    name="project_id"
                    required
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                >
                    <option value="">-- Choisir une matière --</option>

                    @foreach($projectsMaster as $project)
                        <option
                            value="{{ $project->id }}"
                            @selected(old('project_id') == $project->id)
                        >
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Date de l'examen *
                </label>

                <input
                    type="date"
                    name="exam_date"
                    value="{{ old('exam_date') }}"
                    required
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Heure de début
                </label>

                <input
                    type="time"
                    name="start_time"
                    value="{{ old('start_time') }}"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Durée en minutes
                </label>

                <input
                    type="number"
                    name="duration_minutes"
                    min="1"
                    max="1440"
                    value="{{ old('duration_minutes') }}"
                    placeholder="Ex : 120"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Note visée / 20
                </label>

                <input
                    type="number"
                    name="target_score"
                    min="0"
                    max="20"
                    step="0.25"
                    value="{{ old('target_score') }}"
                    placeholder="Ex : 16"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                >
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Notes ou consignes
                </label>

                <textarea
                    name="notes"
                    rows="3"
                    placeholder="Ex : chapitres à maîtriser, consignes de l'enseignant..."
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
                >{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-2 md:col-span-2">

                <button
                    type="button"
                    @click="open = false"
                    class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700"
                >
                    Annuler
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-5 py-2 text-xs font-bold text-white hover:bg-blue-700"
                >
                    💾 Enregistrer l'examen
                </button>

            </div>
        </form>

        {{-- TABLEAU DES EXAMENS --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-xs">

                <thead class="border-b border-blue-100 bg-blue-50">
                    <tr class="font-extrabold uppercase text-slate-600">
                        <th class="px-4 py-3">Matière</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Heure</th>
                        <th class="px-4 py-3">Durée</th>
                        <th class="px-4 py-3">Objectif</th>                      
                        <th class="px-4 py-3">Compte à rebours</th>
                        <th class="px-4 py-3 text-right">Actions</th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($exams as $exam)

                        @php
                            $daysRemaining = (int) $today->diffInDays(
                                $exam->exam_date,
                                false
                            );
                        @endphp

                        <tr class="hover:bg-blue-50/50">

                            <td class="px-4 py-4 font-bold text-slate-900">
                                📚 {{ $exam->project->title ?? 'Matière supprimée' }}
                            </td>

                            <td class="px-4 py-4 text-slate-600">
                                {{ $exam->exam_date->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-4 text-slate-600">
                                {{ $exam->start_time
                                    ? substr($exam->start_time, 0, 5)
                                    : '—' }}
                            </td>

                            <td class="px-4 py-4 text-slate-600">
                                {{ $exam->duration_minutes
                                    ? $exam->duration_minutes . ' min'
                                    : '—' }}
                            </td>

                            <td class="px-4 py-4 font-semibold text-blue-700">
                                {{ $exam->target_score !== null
                                    ? $exam->target_score . '/20'
                                    : '—' }}
                            </td>

                            <td class="px-4 py-4">
                                @if($daysRemaining < 0)
                                    <span class="font-bold text-slate-500">
                                        Passé
                                    </span>
                                @elseif($daysRemaining === 0)
                                    <span class="font-bold text-amber-700">
                                        Aujourd'hui
                                    </span>
                                @else
                                    <span class="font-bold text-blue-700">
                                        J-{{ $daysRemaining }}
                                    </span>

                                @endif
                                </td>

                                {{-- ACTIONS --}}
                                <td class="px-4 py-4 text-right">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- MODIFIER --}}
                                        <button
                                            type="button"
                                            onclick="document.getElementById('editExam-{{ $exam->id }}').showModal()"
                                            title="Modifier l'examen"
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white hover:bg-blue-700"
                                        >
                                            ✎
                                        </button>

                                        {{-- SUPPRIMER --}}
                                        <form
                                            action="{{ route('exams.destroy', $exam->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Supprimer définitivement cet examen ?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Supprimer l'examen"
                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white hover:bg-red-600"
                                            >
                                                🗑
                                            </button>
                                        </form>

                                    </div>

                                    {{-- FENÊTRE DE MODIFICATION --}}
                                    <dialog
                                        id="editExam-{{ $exam->id }}"
                                        class="w-[95%] max-w-xl rounded-2xl border border-blue-100 bg-white p-0 text-left shadow-2xl backdrop:bg-slate-900/50"
                                    >
                                        <div class="border-b border-slate-100 px-5 py-4">
                                            <h3 class="text-base font-extrabold text-slate-900">
                                                ✏️ Modifier l'examen
                                            </h3>

                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $exam->project->title ?? 'Matière' }}
                                            </p>
                                        </div>

                                        <form
                                            action="{{ route('exams.update', $exam->id) }}"
                                            method="POST"
                                            class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <div class="sm:col-span-2">
                                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                                    Matière *
                                                </label>

                                                <select
                                                    name="project_id"
                                                    required
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                                                >
                                                    @foreach($projectsMaster as $project)
                                                        <option
                                                            value="{{ $project->id }}"
                                                            @selected($exam->project_id == $project->id)
                                                        >
                                                            {{ $project->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                                    Date de l'examen *
                                                </label>

                                                <input
                                                    type="date"
                                                    name="exam_date"
                                                    value="{{ $exam->exam_date->format('Y-m-d') }}"
                                                    required
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                                                >
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                                    Heure de début
                                                </label>

                                                <input
                                                    type="time"
                                                    name="start_time"
                                                    value="{{ $exam->start_time ? substr($exam->start_time, 0, 5) : '' }}"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                                                >
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                                    Durée (minutes)
                                                </label>

                                                <input
                                                    type="number"
                                                    name="duration_minutes"
                                                    min="1"
                                                    max="1440"
                                                    value="{{ $exam->duration_minutes }}"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                                                >
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                                    Note visée /20
                                                </label>

                                                <input
                                                    type="number"
                                                    name="target_score"
                                                    min="0"
                                                    max="20"
                                                    step="0.01"
                                                    value="{{ $exam->target_score }}"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                                                >
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label class="mb-1 block text-xs font-bold text-slate-700">
                                                    Notes / Consignes
                                                </label>

                                                <textarea
                                                    name="notes"
                                                    rows="3"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm"
                                                >{{ $exam->notes }}</textarea>
                                            </div>

                                            <div class="flex justify-end gap-2 sm:col-span-2">

                                                <button
                                                    type="button"
                                                    onclick="document.getElementById('editExam-{{ $exam->id }}').close()"
                                                    class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100"
                                                >
                                                    Annuler
                                                </button>

                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-blue-600 px-5 py-2 text-xs font-bold text-white hover:bg-blue-700"
                                                >
                                                    Enregistrer les modifications
                                                </button>

                                            </div>
                                        </form>
                                    </dialog>

                                </td>

                                </tr>

                    @empty

                        <tr>
                            <td
                                colspan="7"
                                class="px-4 py-12 text-center text-slate-500"
                            >
                                Aucun examen enregistré.
                                Utilisez « + Nouvel examen » pour commencer.
                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

    </section>

</div>

@endsection
