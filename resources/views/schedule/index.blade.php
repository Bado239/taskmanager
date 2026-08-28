@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto p-6">


    <h1 class="text-2xl font-bold mb-2">

        🎓 MASTER - Emploi du temps

    </h1>


    <p class="text-gray-500 mb-6">

        Consultez votre planning académique.

    </p>



    {{-- MESSAGE --}}

    @if(session('success'))

    <div class="bg-green-100 text-green-700 p-3 rounded mb-5">

        {{ session('success') }}

    </div>

    @endif





    {{-- FORMULAIRE AJOUT --}}

    <div class="bg-white rounded-xl shadow p-5 mb-8">


        <h2 class="font-bold text-lg mb-4">

            📅 Ajouter un emploi du temps

        </h2>



        <form
        action="{{ route('schedule.store') }}"
        method="POST"
        enctype="multipart/form-data">


        @csrf



        <input
        type="text"
        name="title"
        placeholder="Nom de l'emploi du temps"
        class="border rounded p-2 w-full mb-3">



        <input
        type="file"
        name="file"
        accept=".pdf,.png,.jpg,.jpeg"
        class="border rounded p-2 w-full mb-4">



        <button
        class="bg-blue-600 text-white px-5 py-2 rounded-lg">

        💾 Enregistrer

        </button>



        </form>


    </div>





    {{-- AFFICHAGE --}}


    @if($schedule)


    <div class="bg-white rounded-xl shadow p-5">


        <h2 class="font-bold text-lg mb-4">

            📚 {{ $schedule->title }}

        </h2>




        @if($schedule->type === 'pdf')


        <iframe

        src="{{ $schedule->file_path }}"

        class="w-full h-[700px] rounded border">

        </iframe>



        @else



        <img

        src="{{ $schedule->file_path }}"

        class="max-w-full rounded shadow">



        @endif



    </div>


    @else


    <div class="text-center text-gray-400 py-10">

        Aucun emploi du temps ajouté.

    </div>


    @endif



</div>


@endsection