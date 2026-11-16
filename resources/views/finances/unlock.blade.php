
@extends('layouts.app')

@section('content')

<div class="flex min-h-[75vh] items-center justify-center px-4 py-10">

    <div class="w-full max-w-md overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-xl">

        {{-- EN-TÊTE --}}
        <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-blue-800 px-8 py-9 text-center">

            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border border-white/20 bg-white/10 text-3xl">
                🔐
            </div>

            <h1 class="text-2xl font-extrabold text-white">
                Mes Finances
            </h1>

            <p class="mt-2 text-sm text-blue-100">
                Espace financier personnel sécurisé
            </p>
        </div>

        {{-- FORMULAIRE --}}
        <div class="p-7 sm:p-8">

            <p class="mb-6 text-center text-sm text-slate-500">
                Saisissez votre mot de passe pour accéder
                à votre tableau de bord financier.
            </p>

            @if(session('success'))
                <div class="mb-4 rounded-xl bg-emerald-50 p-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 p-3 text-sm font-medium text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form
                action="{{ route('finances.unlock.submit') }}"
                method="POST"
                class="space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="financePassword"
                        class="mb-2 block text-sm font-bold text-slate-700"
                    >
                        Mot de passe
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            id="financePassword"
                            name="password"
                            required
                            autofocus
                            autocomplete="off"
                            placeholder="Votre mot de passe"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-14 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                        <button
                            type="button"
                            onclick="
                                const input = document.getElementById('financePassword');
                                input.type = input.type === 'password'
                                    ? 'text'
                                    : 'password';
                            "
                            class="absolute inset-y-0 right-3 text-xs font-semibold text-blue-600 hover:text-blue-800"
                        >
                            Voir
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800"
                >
                    🔓 Déverrouiller Mes Finances
                </button>
            </form>

            <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                <a
                    href="{{ route('dashboard', ['view' => 'dashboard']) }}"
                    class="text-xs font-semibold text-slate-500 transition hover:text-blue-700"
                >
                    ← Retour au Dashboard
                </a>
            </div>

        </div>
    </div>

</div>

@endsection
