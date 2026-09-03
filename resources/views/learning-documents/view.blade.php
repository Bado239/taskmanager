@extends('layouts.app')


@section('content')


<div class="max-w-6xl mx-auto">


<div class="bg-white rounded-2xl shadow border p-6">


<h1 class="text-2xl font-bold mb-5">

📖 {{ $document->title }}

</h1>



<div class="border rounded-xl overflow-hidden">


<iframe

src="{{ $url }}#toolbar=1"

width="100%"

height="900px"

class="w-full">

</iframe>


</div>


</div>


</div>


@endsection