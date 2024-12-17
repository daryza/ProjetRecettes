@extends('layouts.app')

@section('title', 'Créer une Recette')

@section('content')
    <h1>Créer une Recette</h1>

    <!-- Affichage des messages de succès ou erreur -->
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

    <!-- Formulaire création de recette -->
    <form action="{{ route('recettes.store') }}" method="POST" enctype="multipart/form-data" id="recette-form">
        @csrf
        <label for="nom">Nom de la Recette :</label>
        <input type="text" id="nom" name="nom" required>

        <label for="type_recette">Type de Recette :</label>
        <select id="type_recette" name="type_recettes_id" required>
            <option value="">-- Sélectionnez un type --</option>
            @foreach ($typesRecettes as $typeRecette)
                <option value="{{ $typeRecette->id }}">{{ $typeRecette->nom }}</option>
            @endforeach
        </select>

        <div id="ajouter-ingredient">
            <h3>Ajouter un ingrédient</h3>

            <label for="ingredient">Ingrédient :</label>
            <select id="ingredient" name="ingredients[]">
                <option value="">-- Sélectionnez un ingrédient --</option>
                @foreach ($categories as $categorie => $ingredients)
                    <optgroup label="{{ $categorie }}">
                        @foreach ($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}">{{ $ingredient->nom }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>

            <label for="quantite">Quantité :</label>
            <input type="number" id="quantite" name="ingredients_quantites[]" placeholder="Quantité" min="0">

            <label for="unite">Unité :</label>
            <select id="unite" name="ingredients_unites[]">
                <option value="g">g</option>
                <option value="kg">kg</option>
                <option value="ml">mL</option>
                <option value="l">L</option>
                <option value="unité">Unité</option>

            </select>

            <button type="button" id="ajouter-ingredient-btn">Ajouter cet ingrédient</button>
        </div>

        <div id="ingredients-ajoutes">
            <h3>Ingrédients ajoutés</h3>
            <ul id="liste-ingredients"></ul>
        </div>

        <label for="temps_cuisson">Temps de Cuisson (min) :</label>
        <input type="number" id="temps_cuisson" name="temps_cuisson"min="0">

        <label for="preparation">Préparation :</label>
        <textarea id="preparation" name="preparation" required></textarea>

        <label for="photo">Photo :</label>
        <input type="file" id="photo" name="photo" accept="image/*">

        <button type="submit">Créer</button>
    </form>

    <hr>

    <!-- Formulaire pour créer un nouvel ingrédient (réservé aux admin) -->
    @if (auth()->check() && auth()->user()->isAdmin())
        <h2>Créer un Nouvel Ingrédient</h2>
        <form action="{{ route('ingredients.store') }}" method="POST">
            @csrf
            <label for="ingredient_nom">Nom de l'Ingrédient :</label>
            <input type="text" id="ingredient_nom" name="nom" required>

            <label for="type_ingredient_id">Type d'Ingrédient :</label>
            <select id="type_ingredient_id" name="type_ingredient_id" required>
                @foreach ($listeCategories as $categorie)
                    <option value="{{ $categorie->id }}">{{ $categorie->nom }}</option>
                @endforeach
            </select>

            <button type="submit">Créer l'Ingrédient</button>
        </form>
    @endif

    <script>
        console.log('Formulaire chargé avec succès.');

        // Ajout de logs pour Type de Recette
        document.getElementById('type_recette').addEventListener('change', function () {
            console.log('Type de recette sélectionné : ', this.value);
        });

        document.getElementById('ajouter-ingredient-btn').addEventListener('click', function () {
            const ingredientSelect = document.getElementById('ingredient');
            const quantiteInput = document.getElementById('quantite');
            const uniteSelect = document.getElementById('unite');
            const form = document.getElementById('recette-form'); // Formulaire ciblé explicitement

            console.log('Bouton "Ajouter cet ingrédient" cliqué.');
            console.log('Sélection actuelle : ', {
                ingredient: ingredientSelect.value,
                quantite: quantiteInput.value,
                unite: uniteSelect.value,
            });

            if (!ingredientSelect.value) {
                alert('Veuillez sélectionner un ingrédient.');
                console.error('Aucun ingrédient sélectionné.');
                return;
            }

            if (!quantiteInput.value) {
                alert('Veuillez saisir une quantité.');
                console.error('Quantité non saisie.');
                return;
            }

            const ingredientText = ingredientSelect.options[ingredientSelect.selectedIndex].text;

            // Ajoute l'ingrédient à la liste visible
            const liste = document.getElementById('liste-ingredients');
            const li = document.createElement('li');
            li.textContent = `${ingredientText} - ${quantiteInput.value} ${uniteSelect.value}`;
            li.dataset.ingredientId = ingredientSelect.value; // Stocke l'ID de l'ingrédient
            liste.appendChild(li);

            console.log('Ingrédient ajouté à la liste visible : ', {
                nom: ingredientText,
                quantite: quantiteInput.value,
                unite: uniteSelect.value,
            });

            // Ajoute les champs cachés au formulaire
            form.appendChild(createHiddenInput('ingredients[]', ingredientSelect.value));
            form.appendChild(createHiddenInput('ingredients_quantites[]', quantiteInput.value));
            form.appendChild(createHiddenInput('ingredients_unites[]', uniteSelect.value));

            console.log('Champs cachés ajoutés au formulaire.');

            // Réinitialise les champs pour un nouvel ajout
            ingredientSelect.value = '';
            quantiteInput.value = '';
            uniteSelect.value = 'g';
        });

        // Fonction utilitaire pour créer un champ caché
        function createHiddenInput(name, value) {
            console.log(`Création d'un champ caché : ${name} = ${value}`);
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            return input;
        }
    </script>
@endsection
