@extends('layouts.app') 
@section('title', 'Mon Compte') 

@section('content')
    <h1>Mon Compte</h1>
    
    @auth <!-- Si l'utilisateur est connecté -->
        <p>Pseudo : {{ Auth::user()->pseudo }}</p> <!-- Affiche le pseudo -->
        
        <a href="{{ route('recettes.mes_recettes') }}">Mes Recettes</a> <!-- Lien vers les recettes -->
        
        <!-- Formulaire de déconnexion -->
        <form action="{{ route('deconnexion') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Déconnexion</button>
        </form>

        <!-- Formulaire de suppression de compte -->
        <form action="{{ route('users.destroy', Auth::user()->id) }}" method="POST" 
              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ?')" style="margin-top: 20px;">
            @csrf
            @method('DELETE')
            <button type="submit" style="background-color: red; color: white; border: none; border-radius: 5px;">
                Supprimer mon compte
            </button>
        </form>
    @else <!-- Si l'utilisateur n'est pas connecté -->
        <!-- Formulaire de connexion -->
        <h2>Connexion</h2>
        <form action="{{ route('connexion') }}" method="POST">
            @csrf
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>

        <!-- Formulaire de création de compte -->
        <h2>Créer un compte</h2>
        <form action="{{ route('inscription') }}" method="POST" autocomplete="off">
            @csrf
            <label for="pseudo_inscription">Pseudo :</label>
            <input type="text" id="pseudo_inscription" name="pseudo" required>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirmation">Confirmer le mot de passe :</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>

            <button type="submit">Créer un compte</button>
        </form>

        <!-- Affiche les erreurs de validation -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Message de succès -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    @endauth
@endsection
