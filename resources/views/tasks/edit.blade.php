<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Modifier activité - TaskManagerPRO</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>


<body class="bg-gray-100">


<div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">


    <div class="bg-white shadow-sm rounded-lg overflow-hidden">


        <div class="p-6 border-b bg-gray-50">

            <h2 class="text-xl font-bold text-gray-800">
                ✏️ Modifier l'activité
            </h2>

        </div>



        <div class="p-6">


            @if ($errors->any())

                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">

                    <ul class="list-disc pl-5">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif




            <form action="{{ route('tasks.update',$task->id) }}"
                  method="POST"
                  class="space-y-5">


                @csrf
                @method('PUT')



                <input type="hidden"
                       name="type"
                       value="{{ $task->type ?? 'office' }}">



                <!-- TITRE -->

                <div>

                    <label class="block text-sm font-medium text-gray-700">
                        Titre de l'activité
                    </label>


                    <input type="text"
                           name="title"
                           value="{{ old('title',$task->title) }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                           required>

                </div>




                <!-- CATEGORIE -->

                <div>

                    <label class="block text-sm font-medium text-gray-700">
                        Catégorie
                    </label>


                    <select name="category_id"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                            required>


                        @foreach($categories as $category)

                            <option value="{{ $category->id }}"
                            {{ old('category_id',$task->category_id)==$category->id?'selected':'' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach


                    </select>


                </div>





                <!-- PROJET -->

                <div>


                    <label class="block text-sm font-medium text-gray-700">
                        Projet associé
                    </label>


                    <select name="project_id"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm">


                        <option value="">
                            Aucun projet
                        </option>



                        @foreach($projects as $project)


                            <option value="{{ $project->id }}"
                            {{ old('project_id',$task->project_id)==$project->id?'selected':'' }}>

                                {{ $project->title }}

                            </option>


                        @endforeach


                    </select>


                </div>





                <!-- PRIORITE -->

                <div>


                    <label class="block text-sm font-medium text-gray-700">
                        Priorité
                    </label>


                    <select name="priority"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm">


                        <option value="high"
                        {{ old('priority',$task->priority)=='high'?'selected':'' }}>
                            🔴 Haute
                        </option>


                        <option value="medium"
                        {{ old('priority',$task->priority)=='medium'?'selected':'' }}>
                            🟡 Moyenne
                        </option>


                        <option value="low"
                        {{ old('priority',$task->priority)=='low'?'selected':'' }}>
                            🔵 Basse
                        </option>


                    </select>


                </div>





                <!-- STATUT -->

                <div>


                    <label class="block text-sm font-medium text-gray-700">
                        Statut
                    </label>


                    <select name="document_status"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm">


                        <option value="todo"
                        {{ old('document_status',$task->document_status)=='todo'?'selected':'' }}>
                            🔴 À faire
                        </option>


                        <option value="in_progress"
                        {{ old('document_status',$task->document_status)=='in_progress'?'selected':'' }}>
                            🟡 En cours
                        </option>


                        <option value="done"
                        {{ old('document_status',$task->document_status)=='done'?'selected':'' }}>
                            🟢 Validée
                        </option>


                    </select>


                </div>





                <!-- DOCUMENT -->

                <div>


                    <label class="block text-sm font-medium text-gray-700">
                        🔗 Lien document / cours
                    </label>


                    <input type="url"
                           name="document_link"
                           value="{{ old('document_link',$task->document_link) }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                           placeholder="https://...">


                </div>





                <!-- DATE -->

                <div>


                    <label class="block text-sm font-medium text-gray-700">
                        Date prévue
                    </label>


                    <input type="date"
                           name="date_prevue"
                           value="{{ old('date_prevue',$task->date_prevue) }}"
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm">


                </div>





                <!-- HEURES -->

                <div class="grid grid-cols-2 gap-4">


                    <div>

                        <label class="block text-sm font-medium">
                            Heure début
                        </label>


                        <input type="time"
                               id="heure_debut"
                               name="heure_debut"
                               value="{{ old('heure_debut',$task->heure_debut ? substr($task->heure_debut,0,5):'') }}"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm">


                    </div>



                    <div>

                        <label class="block text-sm font-medium">
                            Heure fin
                        </label>


                        <input type="time"
                               id="heure_fin"
                               name="heure_fin"
                               value="{{ old('heure_fin',$task->heure_fin ? substr($task->heure_fin,0,5):'') }}"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm">


                    </div>


                </div>






                <!-- ACTIONS -->


                <div class="flex gap-4 pt-4">


                    <button type="submit"
                            class="flex-1 bg-green-700 hover:bg-green-800 text-white font-bold py-2 rounded-md">

                        💾 Enregistrer

                    </button>



                    <a href="{{ route('dashboard',['view'=>$task->type ?? 'office']) }}"
                       class="flex-1 text-center bg-gray-200 hover:bg-gray-300 py-2 rounded-md">

                        Annuler

                    </a>


                </div>


            </form>


        </div>


    </div>


</div>





<script>

const heureDebut = document.getElementById('heure_debut');


if(heureDebut){

    heureDebut.addEventListener('change',function(){


        let value=this.value;


        if(value){


            let [h,m]=value.split(':').map(Number);


            h += 2;


            if(h>=24){
                h -= 24;
            }


            document.getElementById('heure_fin').value =
            String(h).padStart(2,'0') + ':' +
            String(m).padStart(2,'0');


        }


    });

}


</script>



</body>

</html>