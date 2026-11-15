
@extends('layouts.app')

@section('content')

@php
    $formatMoney = fn ($value) =>
        number_format((float) $value, 0, ',', ' ') . ' F';

    $expenses = $categories->where('kind', 'expense');
    $savings = $categories->where('kind', 'saving');

    $income = (int) $financeMonth->income;

@endphp

<div
    class="mx-auto max-w-7xl space-y-5 pb-12"
    x-data="{
        editBudget: false,
        showMovement: false,
        showCategory: false,
        showIncome: false,
        showDetails: false
    }"
>

    {{-- EN-TÊTE --}}
    <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">

        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>
                <h1 class="text-xl font-extrabold text-slate-900">
                    💰 Mes Finances
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Budget personnel, dépenses et épargne.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                <input
                    type="month"
                    value="{{ $month }}"
                    onchange="window.location.href =
                        @js(route('finances.index')) +
                        '?month=' + encodeURIComponent(this.value)"
                    class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700"
                >

                <button
                    type="button"
                    @click="editBudget = !editBudget"
                    class="rounded-lg border border-blue-200 bg-white px-4 py-2 text-xs font-bold text-blue-700 hover:bg-blue-50"
                >
                    ⚙️ Ajuster le budget
                </button>

            </div>

        </div>
    </section>

    {{-- NOTIFICATIONS --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <strong>Veuillez vérifier les informations saisies.</strong>
            <ul class="mt-2 list-inside list-disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INDICATEURS PRINCIPAUX --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

        <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
            <p class="text-xs font-semibold text-blue-700">
                💵 Revenu prévu
            </p>
            <p class="mt-2 text-xl font-extrabold text-blue-800">
                {{ $formatMoney($income) }}
            </p>
        </div>

        <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
            <p class="text-xs font-semibold text-amber-700">
                🛒 Budget dépenses
            </p>
            <p class="mt-2 text-xl font-extrabold text-amber-800">
                {{ $formatMoney($plannedExpenses) }}
            </p>
        </div>

        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
            <p class="text-xs font-semibold text-emerald-700">
                🐷 Épargne prévue
            </p>
            <p class="mt-2 text-xl font-extrabold text-emerald-800">
                {{ $formatMoney($plannedSavings) }}
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold text-slate-600">
                ⚖️ Solde non affecté
            </p>
            <p class="mt-2 text-xl font-extrabold {{ $unallocated < 0 ? 'text-red-600' : 'text-slate-900' }}">
                {{ $formatMoney($unallocated) }}
            </p>
        </div>

    </div>

    {{-- BOUTON AFFICHER / MASQUER LES DÉTAILS --}}
    <div class="flex justify-end">

        <button
            type="button"
            @click="showDetails = !showDetails"
            class="inline-flex items-center gap-2 rounded-xl
                border border-blue-200 bg-white
                px-5 py-3 text-sm font-bold text-blue-700
                shadow-sm transition hover:bg-blue-50"
        >
            <span x-text="showDetails ? '👁️ Masquer les détails' : '📊 Afficher les détails'"></span>

            <svg
                class="h-4 w-4 transition-transform"
                :class="showDetails ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
            >
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </button>

    </div>

    {{-- AJUSTER LE REVENU --}}
    <section
        x-show="editBudget"
        x-cloak
        class="rounded-xl border border-blue-200 bg-white p-4"
    >
        <form
            action="{{ route('finances.income.update') }}"
            method="POST"
            class="flex flex-wrap items-end gap-3"
        >
            @csrf
            @method('PUT')

            <input type="hidden" name="month" value="{{ $month }}">

            <div class="min-w-[220px] flex-1">
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Modifier le revenu mensuel
                </label>

                <input
                    type="number"
                    name="income"
                    min="0"
                    value="{{ $income }}"
                    required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                >
            </div>

            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700"
            >
                Enregistrer le revenu
            </button>

        </form>
    </section>


    {{-- DÉTAILS FINANCIERS MASQUÉS PAR DÉFAUT --}}
    <div x-show="showDetails" x-cloak class="space-y-5">

    {{-- ACTIONS RAPIDES --}}
    <section class="flex flex-wrap items-center justify-between gap-3">

        <h2 class="text-base font-extrabold text-slate-900">
            📋 Mes postes budgétaires
        </h2>

        <div class="flex flex-wrap gap-2">

            <button
                type="button"
                @click="showCategory = !showCategory"
                class="rounded-lg border border-blue-200 bg-white px-4 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-50"
            >
                + Nouveau poste
            </button>

            <button
                type="button"
                @click="showMovement = !showMovement"
                class="rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700"
            >
                + Nouvelle opération
            </button>

        </div>

    </section>

    {{-- CRÉATION D'UN POSTE --}}
    <section
        x-show="showCategory"
        x-cloak
        class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm"
    >
        <h3 class="mb-4 font-bold text-slate-900">
            ➕ Ajouter un poste budgétaire
        </h3>

        <form
            action="{{ route('finances.categories.store') }}"
            method="POST"
            class="grid grid-cols-1 gap-3 md:grid-cols-4"
        >
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">

            <input
                name="name"
                required
                maxlength="120"
                placeholder="Nom du poste"
                class="rounded-lg border border-slate-200 px-3 py-2 text-sm"
            >

            <select
                name="kind"
                required
                class="rounded-lg border border-slate-200 px-3 py-2 text-sm"
            >
                <option value="expense">Dépense</option>
                <option value="saving">Épargne</option>
            </select>

            <input
                type="number"
                name="planned_amount"
                value="0"
                min="0"
                required
                class="rounded-lg border border-slate-200 px-3 py-2 text-sm"
            >

            <button
                type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white"
            >
                Ajouter le poste
            </button>
        </form>
    </section>

    {{-- NOUVELLE DÉPENSE / ÉPARGNE --}}
    <section
        x-show="showMovement"
        x-cloak
        class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm"
    >
        <h3 class="mb-4 font-bold text-slate-900">
            💳 Enregistrer une opération réelle
        </h3>

        <form
            action="{{ route('finances.movements.store') }}"
            method="POST"
            class="grid grid-cols-1 gap-3 md:grid-cols-2"
        >
            @csrf

            <input type="hidden" name="month" value="{{ $month }}">

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Poste budgétaire *
                </label>
                <select
                    name="category_id"
                    required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                >
                    <option value="">-- Choisir --</option>

                    <optgroup label="Dépenses">
                        @foreach($expenses as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Épargne">
                        @foreach($savings as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Montant (FCFA) *
                </label>
                <input
                    type="number"
                    name="amount"
                    min="1"
                    required
                    placeholder="Ex : 5000"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Date de l'opération *
                </label>
                <input
                    type="date"
                    name="movement_date"
                    min="{{ $month }}-01"
                    max="{{ \Carbon\Carbon::createFromFormat('Y-m-d', $month . '-01')->endOfMonth()->format('Y-m-d') }}"
                    value="{{ now('Africa/Dakar')->format('Y-m') === $month ? now('Africa/Dakar')->format('Y-m-d') : $month . '-01' }}"
                    required
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                >
            </div>

            <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">
                    Description
                </label>
                <input
                    name="description"
                    maxlength="255"
                    placeholder="Ex : achat de ciment, courses..."
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                >
            </div>

            <div class="flex justify-end md:col-span-2">
                <button
                    type="submit"
                    class="rounded-lg bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-700"
                >
                    💾 Enregistrer l'opération
                </button>
            </div>

        </form>
    </section>

    {{-- TABLEAU DES DÉPENSES --}}
    <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="font-extrabold text-slate-900">
                🛒 Budget des dépenses
            </h3>
            <p class="mt-1 text-xs text-slate-500">
                Prévisions comparées aux dépenses réelles.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-blue-50 text-xs uppercase text-slate-700">
                    <tr>
                        <th class="px-4 py-3">Poste</th>
                        <th class="px-4 py-3">Prévu</th>
                        <th class="px-4 py-3">Réalisé</th>
                        <th class="px-4 py-3">Écart restant</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach($expenses as $category)
                        @php
                            $difference = $category->planned_amount
                                - $category->actual_amount;
                        @endphp

                        <tr class="hover:bg-blue-50/40">
                            <td class="px-4 py-3 font-bold text-slate-800">
                                {{ $category->name === 'Construction'
                                    ? '🏗️ '
                                    : '📁 ' }}
                                {{ $category->name }}
                            </td>

                            <td class="px-4 py-3">
                                <form
                                    id="budget-{{ $category->id }}"
                                    action="{{ route('finances.categories.update', $category->id) }}"
                                    method="POST"
                                    class="flex items-center gap-2"
                                >
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="month" value="{{ $month }}">

                                    <span x-show="!editBudget">
                                        {{ $formatMoney($category->planned_amount) }}
                                    </span>

                                    <input
                                        x-show="editBudget"
                                        x-cloak
                                        type="number"
                                        name="planned_amount"
                                        min="0"
                                        value="{{ $category->planned_amount }}"
                                        class="w-28 rounded-lg border border-blue-200 px-2 py-1.5 text-sm"
                                    >

                                    <button
                                        x-show="editBudget"
                                        x-cloak
                                        type="submit"
                                        title="Enregistrer le budget"
                                        class="rounded-md bg-blue-600 px-2 py-1.5 text-xs text-white"
                                    >
                                        ✓
                                    </button>
                                </form>
                            </td>

                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $formatMoney($category->actual_amount) }}
                            </td>

                            <td class="px-4 py-3 font-semibold {{ $difference < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                {{ $formatMoney($difference) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <form
                                    action="{{ route('finances.categories.destroy', $category->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Supprimer ce poste budgétaire ?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="month" value="{{ $month }}">

                                    <button
                                        x-show="editBudget"
                                        x-cloak
                                        type="submit"
                                        class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100"
                                    >
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot class="border-t border-blue-100 bg-blue-50 font-extrabold text-slate-900">
                    <tr>
                        <td class="px-4 py-4">Total dépenses</td>
                        <td class="px-4 py-4">{{ $formatMoney($plannedExpenses) }}</td>
                        <td class="px-4 py-4">{{ $formatMoney($actualExpenses) }}</td>
                        <td class="px-4 py-4">{{ $formatMoney($plannedExpenses - $actualExpenses) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    {{-- TABLEAU DE L'ÉPARGNE --}}
    <section class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="font-extrabold text-slate-900">
                🐷 Mes objectifs d'épargne
            </h3>
            <p class="mt-1 text-xs text-slate-500">
                Objectifs mensuels et versements réellement effectués.
            </p>
        </div>

        <div class="divide-y divide-slate-100">
            @foreach($savings as $category)
                @php
                    $progress = $category->planned_amount > 0
                        ? min(100, $category->actual_amount / $category->planned_amount * 100)
                        : 0;
                @endphp

                <div class="space-y-3 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <strong class="text-sm text-slate-900">
                            {{ $category->name }}
                        </strong>

                        <span class="text-xs font-semibold text-emerald-700">
                            {{ $formatMoney($category->actual_amount) }}
                            /
                            {{ $formatMoney($category->planned_amount) }}
                        </span>
                    </div>

                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                        <div
                            class="h-full rounded-full bg-emerald-500"
                            style="width: {{ $progress }}%"
                        ></div>
                    </div>

                    <div
                        x-show="editBudget"
                        x-cloak
                        class="flex flex-wrap items-center gap-2"
                    >
                        <form
                            action="{{ route('finances.categories.update', $category->id) }}"
                            method="POST"
                            class="flex items-center gap-2"
                        >
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="month" value="{{ $month }}">

                            <input
                                name="planned_amount"
                                type="number"
                                min="0"
                                value="{{ $category->planned_amount }}"
                                class="w-40 rounded-lg border border-slate-200 px-3 py-2 text-sm"
                            >

                            <button
                                type="submit"
                                class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white"
                            >
                                Modifier
                            </button>
                        </form>

                        <form
                            action="{{ route('finances.categories.destroy', $category->id) }}"
                            method="POST"
                            onsubmit="return confirm('Supprimer cet objectif ?');"
                        >
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="month" value="{{ $month }}">

                            <button
                                type="submit"
                                class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600"
                            >
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap justify-between gap-2 border-t border-emerald-100 bg-emerald-50 px-5 py-4 text-sm font-extrabold text-emerald-800">
            <span>Total épargne</span>
            <span>
                {{ $formatMoney($actualSavings) }} versés
                /
                {{ $formatMoney($plannedSavings) }} prévus
            </span>
        </div>

    </section>

    {{-- SYNTHÈSE RÉELLE --}}
    <section class="rounded-2xl border border-blue-100 bg-white p-5 shadow-sm">
        <h3 class="font-extrabold text-slate-900">
            📈 Situation financière du mois
        </h3>

        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Dépenses enregistrées</p>
                <p class="mt-1 text-lg font-extrabold text-amber-700">
                    {{ $formatMoney($actualExpenses) }}
                </p>
            </div>

            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Épargne enregistrée</p>
                <p class="mt-1 text-lg font-extrabold text-emerald-700">
                    {{ $formatMoney($actualSavings) }}
                </p>
            </div>

            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs text-slate-500">Reste théorique</p>
                <p class="mt-1 text-lg font-extrabold {{ $remaining < 0 ? 'text-red-600' : 'text-blue-700' }}">
                    {{ $formatMoney($remaining) }}
                </p>
            </div>
        </div>

        <p class="mt-3 text-xs text-slate-500">
            Le reste théorique est calculé à partir du revenu mensuel
            prévu, diminué des dépenses et versements saisis.
            Il ne représente pas nécessairement le solde bancaire réel.
        </p>
    </section>

    {{-- HISTORIQUE --}}
    <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4">
            <h3 class="font-extrabold text-slate-900">
                🧾 Historique des opérations
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left text-sm">
                <thead class="bg-blue-50 text-xs uppercase text-slate-700">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Poste</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Montant</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-600">
                                {{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $movement->category_name }}
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $movement->description ?: '—' }}
                            </td>

                            <td class="px-4 py-3 font-bold {{ $movement->kind === 'saving' ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $formatMoney($movement->amount) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <form
                                    action="{{ route('finances.movements.destroy', $movement->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Supprimer cette opération ?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="month" value="{{ $month }}">

                                    <button
                                        type="submit"
                                        title="Supprimer l'opération"
                                        class="rounded-lg bg-red-50 px-3 py-2 text-xs text-red-600 hover:bg-red-100"
                                    >
                                        🗑 Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="px-4 py-10 text-center text-slate-500"
                            >
                                Aucune dépense ou épargne enregistrée pour ce mois.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </section>

    {{-- FIN DES DÉTAILS FINANCIERS --}}
    </div>

</div>

@endsection