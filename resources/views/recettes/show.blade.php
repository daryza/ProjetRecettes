@extends('layouts.app')

@section('title', $recette->nom)

@section('content')
    <h1>{{ $recette->nom }}</h1>

    <!-- Affiche le cpseudo créateur de la recette -->
    <p>Créateur de la recette : {{ $recette->user->pseudo ?? 'Inconnu' }}</p>
    
    <!-- Affiche le type de recette -->
    <p>Type : {{ $recette->typeRecette->nom ?? 'Non défini' }}</p>
    
    <p><strong>Temps de cuisson :</strong> {{ $recette->temps_cuisson ?? 'Non spécifié' }} minutes</p>
    <p><strong>Préparation :</strong></p>
    <p>{{ $recette->preparation }}</p>

    <h2>Ingrédients</h2>
    <ul>
        @foreach ($recette->ingredients as $ingredient)
            <li>
                {{ $ingredient->nom }} : 
                {{ $ingredient->pivot->quantite }} 
                {{ $ingredient->pivot->unite }}
            </li>
        @endforeach
    </ul>

    <!-- Affiche la photo si elle existe -->
    @if ($recette->photo)
        <img src="{{ asset('storage/' . $recette->photo) }}" alt="Photo de {{ $recette->nom }}" class="recette-photo">
    @endif

    <!-- Boutons pdf -->
    <div style="margin-top: 20px; display: flex; gap: 15px;">
        <!-- Téléchargement pdf -->
        <a href="{{ route('recettes.pdf', $recette->id) }}" 
           style="padding: 10px 20px; background-color: green; color: white; border: none; border-radius: 5px; text-decoration: none; font-size: 16px;">
           Télécharger en PDF
        </a>

        <!-- Bouton Modifier (uniquement pour le créateur de la recette) -->
        @if (auth()->check() && auth()->user()->id === $recette->users_id)
            <a href="{{ route('recettes.edit', ['recette' => $recette->id]) }}" 
               style="padding: 10px 20px; background-color: blue; color: white; border: none; border-radius: 5px; text-decoration: none; font-size: 16px;">
               Modifier
            </a>
        @endif

        <!-- Bouton Supprimer (uniquement pour créateur ou un admin) -->
        @auth
            @if(auth()->id() === $recette->users_id || auth()->user()->isAdmin())
                <form action="{{ route('recettes.destroy', $recette->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?');" 
                        style="padding: 10px 20px; background-color: red; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                        Supprimer cette recette
                    </button>
                </form>
            @endif
        @endauth
    </div>
@endsection
