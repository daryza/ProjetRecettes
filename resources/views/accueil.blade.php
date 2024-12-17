@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <h1>Bienvenue sur Les Recettes du Programmeur</h1>
    
    <!-- Suggestion du chef -->
    <div class="recette-du-mois">
        <h2>Suggestion du Chef</h2>
        <a href="{{ route('recettes.show', $recetteDuMois->id) }}">{{ $recetteDuMois->nom }}</a>
    </div>
    
    <!-- Dernières Recettes Créées -->
    <div class="dernieres-recettes">
        <h2>Dernières Recettes Créées</h2>
        <ul>
            @foreach ($dernieresRecettes as $recette)
                <li><a href="{{ route('recettes.show', $recette->id) }}">{{ $recette->nom }}</a></li>
            @endforeach
        </ul>
    </div>
@endsection
