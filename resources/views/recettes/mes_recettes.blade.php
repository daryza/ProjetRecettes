@extends('layouts.app')

@section('title', 'Mes Recettes')

@section('content')
    <h1>Mes Recettes</h1>
    @if($recettes->isEmpty())
        <p>Vous n'avez pas encore créé de recettes.</p>
    @else
        <ul>
            @foreach($recettes as $recette)
                <li>
                    <a href="{{ route('recettes.show', $recette->id) }}">{{ $recette->nom }}</a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
