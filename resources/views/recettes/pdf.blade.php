<!DOCTYPE html>
<html>
<head>
    <title>{{ $recette->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .ingredients {
            margin-bottom: 20px;
        }
        .preparation {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>{{ $recette->nom }}</h1>
    <p><strong>Type :</strong> {{ $recette->typeRecette->nom ?? 'Non défini' }}</p>
    <p><strong>Temps de cuisson :</strong> {{ $recette->temps_cuisson ?? 'Non spécifié' }} minutes</p>

    <h2>Ingrédients :</h2>
    <ul class="ingredients">
        @foreach ($recette->ingredients as $ingredient)
            <li>{{ $ingredient->nom }} : {{ $ingredient->pivot->quantite }} {{ $ingredient->pivot->unite }}</li>
        @endforeach
    </ul>

    <h2>Préparation :</h2>
    <p class="preparation">{{ $recette->preparation }}</p>
</body>
</html>
