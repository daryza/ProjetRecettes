@extends('layouts.app')

@section('content')
<h1>Modifier la recette : {{ $recette->nom }}</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('recettes.update', ['recette' => $recette->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Nom de la recette -->
    <div class="form-group row mb-3">
        <label for="nom" class="col-sm-2 col-form-label">Nom</label>
        <div class="col-sm-10">
            <input type="text" name="nom" id="nom" value="{{ old('nom', $recette->nom) }}" class="form-control" required>
        </div>
    </div>

    <!-- Type de recette -->
    <div class="form-group row mb-3">
        <label for="type_recettes_id" class="col-sm-2 col-form-label">Type de recette</label>
        <div class="col-sm-10">
            <select name="type_recettes_id" id="type_recettes_id" class="form-control" required>
                <option value="">-- Sélectionnez un type --</option>
                @foreach ($typesRecettes as $typeRecette)
                    <option value="{{ $typeRecette->id }}" {{ $recette->type_recettes_id == $typeRecette->id ? 'selected' : '' }}>
                        {{ $typeRecette->nom }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Préparation -->
    <div class="form-group row mb-3">
        <label for="preparation" class="col-sm-2 col-form-label">Préparation</label>
        <div class="col-sm-10">
            <textarea name="preparation" id="preparation" class="form-control" rows="4" required>{{ old('preparation', $recette->preparation) }}</textarea>
        </div>
    </div>

    <!-- Temps de cuisson -->
    <div class="form-group row mb-3">
        <label for="temps_cuisson" class="col-sm-2 col-form-label">Temps de cuisson</label>
        <div class="col-sm-10">
            <input type="number" name="temps_cuisson" id="temps_cuisson" value="{{ old('temps_cuisson', $recette->temps_cuisson) }}" class="form-control" required>
        </div>
    </div>

    <!-- Gestion des ingrédients -->
    <h3>Ingrédients</h3>
    <div id="ingredients-list" class="mb-3">
        @foreach ($recette->ingredients as $ingredient)
            <div class="ingredient-group d-flex align-items-center mb-2" data-ingredient-id="{{ $ingredient->id }}">
                <label class="me-2">Ingrédient :</label>
                <select name="ingredients[]" class="form-control me-2" required>
                    @foreach ($categories as $categorie => $ingredients)
                        <optgroup label="{{ $categorie }}">
                            @foreach ($ingredients as $item)
                                <option value="{{ $item->id }}" 
                                    {{ $ingredient->id == $item->id ? 'selected' : '' }}>
                                    {{ $item->nom }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <label class="me-2">Quantité :</label>
                <input type="number" name="ingredients_quantites[]" value="{{ $ingredient->pivot->quantite }}" class="form-control me-2" required>

                <label class="me-2">Unité :</label>
                <select name="ingredients_unites[]" class="form-control me-2" required>
                    <option value="g" {{ $ingredient->pivot->unite == 'g' ? 'selected' : '' }}>g</option>
                    <option value="kg" {{ $ingredient->pivot->unite == 'kg' ? 'selected' : '' }}>kg</option>
                    <option value="ml" {{ $ingredient->pivot->unite == 'ml' ? 'selected' : '' }}>mL</option>
                    <option value="l" {{ $ingredient->pivot->unite == 'l' ? 'selected' : '' }}>L</option>
                </select>

                <!-- Bouton pour retirer l'ingrédient -->
                <button type="button" class="btn btn-danger remove-ingredient-btn">Supprimer</button>
            </div>
        @endforeach
    </div>

    <!-- Ajouter un nouvel ingrédient -->
    <div class="mb-3">
        <button type="button" id="ajouter-ingredient-btn" class="btn btn-primary">Ajouter un ingrédient</button>
    </div>

    <!-- Photo -->
    <div class="form-group mb-3">
        <label for="photo">Modifier la photo</label>
        <input type="file" name="photo" id="photo" class="form-control">
        @if ($recette->photo)
            <p class="mt-2">Photo actuelle :</p>
            <img src="{{ asset('storage/' . $recette->photo) }}" alt="Photo actuelle" class="img-thumbnail" style="max-width: 200px;">
        @endif
    </div>

    <!-- Bouton de mise à jour -->
    <button type="submit" class="btn btn-success">Mettre à jour</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gestion des boutons "Supprimer"
        document.querySelectorAll('.remove-ingredient-btn').forEach(button => {
            button.addEventListener('click', function () {
                const ingredientGroup = this.closest('.ingredient-group');
                ingredientGroup.remove(); // Supprime visuellement l'ingrédient
            });
        });

        // Bouton pour ajouter un nouvel ingrédient
        document.getElementById('ajouter-ingredient-btn').addEventListener('click', function () {
            const ingredientList = document.getElementById('ingredients-list');
            const newIngredient = document.createElement('div');
            newIngredient.classList.add('ingredient-group', 'd-flex', 'align-items-center', 'mb-2');
            newIngredient.innerHTML = `
                <label class="me-2">Ingrédient :</label>
                <select name="ingredients[]" class="form-control me-2" required>
                    <option value="">-- Sélectionnez un ingrédient --</option>
                    @foreach ($categories as $categorie => $ingredients)
                        <optgroup label="{{ $categorie }}">
                            @foreach ($ingredients as $item)
                                <option value="{{ $item->id }}">{{ $item->nom }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <label class="me-2">Quantité :</label>
                <input type="number" name="ingredients_quantites[]" class="form-control me-2" required>

                <label class="me-2">Unité :</label>
                <select name="ingredients_unites[]" class="form-control me-2" required>
                    <option value="g">g</option>
                    <option value="kg">kg</option>
                    <option value="ml">mL</option>
                    <option value="l">L</option>
                </select>

                <button type="button" class="btn btn-danger remove-ingredient-btn">Supprimer</button>
            `;
            ingredientList.appendChild(newIngredient);

            // événement au bouton "Supprimer"
            newIngredient.querySelector('.remove-ingredient-btn').addEventListener('click', function () {
                newIngredient.remove();
            });
        });
    });
</script>
@endsection
