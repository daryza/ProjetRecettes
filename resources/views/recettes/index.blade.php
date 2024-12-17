@extends('layouts.app')

@section('title', 'Les Recettes')

@section('content')
<div class="conteneur-recettes">
    <h1>Les Recettes</h1>
    <ul class="liste-recettes">
        @foreach ($recettes as $recette)
            <li><a href="{{ route('recettes.show', $recette->id) }}">{{ $recette->nom }}</a></li>
        @endforeach
    </ul>
</div>
@endsection
