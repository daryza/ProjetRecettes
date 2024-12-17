<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Les Recettes du Programmeur')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header>
        <!-- Barre de navigation -->
        <header>

    <nav>
        <ul class="navbar">
            <li><a href="{{ route('accueil') }}">Accueil</a></li>
            <li><a href="{{ route('recettes.index') }}">Recettes</a></li>

            @auth
                <!-- Affichage de l'onglet "Mes Recettes" et "Créer une Recette" si connecté -->
                <li><a href="{{ route('recettes.mes_recettes') }}">Mes Recettes</a></li>
                <li><a href="{{ route('recettes.create') }}">Créer une Recette</a></li>

                <!-- Lien vers page Mon compte et Affichage du pseudo de l'utilisateur connecté -->
                <li>
                    <a href="{{ route('connexion') }}">
                        {{ Auth::user()->pseudo }}
                    </a>
                </li>
                <!-- Bouton de déconnexion -->
                <form action="{{ route('deconnexion') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            @else
                <!-- Affichage de l'onglet "Se connecter" quand non connecté -->
                <li><a href="{{ route('connexion') }}">Se connecter</a></li>
            @endauth
        </ul>
    </nav>
</header>


    </header>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

    <main>
        <!-- Contenu principal -->
        @yield('content')
    </main>
</body>
</html>
