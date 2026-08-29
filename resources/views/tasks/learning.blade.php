<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">


    <h2 class="text-lg font-bold mb-5">
        🌍 Ressources recommandées
    </h2>


    <div class="space-y-4">


    @forelse($task->courseResources as $resource)


    <div class="flex items-center justify-between p-4 rounded-xl bg-blue-50 border border-blue-100">


    <div>

    <h3 class="font-bold text-gray-900">
        {{ $resource->title }}
    </h3>


    <p class="text-sm text-gray-500">
        {{ $resource->source }}
    </p>


    <span class="text-xs text-blue-600 font-semibold">
        {{ strtoupper($resource->type) }}
    </span>


    </div>

<a href="{{ $resource->url }}"
target="_blank"
class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold">

Voir le cours

</a>


</div>


@empty


<p class="text-gray-500">
Aucune ressource ajoutée pour ce chapitre.
</p>


@endforelse


</div>


</div>

        <p class="text-gray-500">
            Les meilleurs cours et documents seront ajoutés ici.
        </p>


    </div>