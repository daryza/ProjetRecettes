<?php

namespace App\Http\Controllers;

use App\Models\IngredientRecette;
use Illuminate\Http\Request;

class IngredientRecettesController extends Controller
{

    // Ajouter un ingrédient à une recette.

    public function store(Request $request)
    {
        $request->validate([
            'recette_id' => 'required|exists:recettes,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantite' => 'required|numeric|min:0',
            'unite' => 'required|string',
        ]);

        IngredientRecette::create($request->only(['recette_id', 'ingredient_id', 'quantite', 'unite']));

        return redirect()->back()->with('success', 'Ingrédient ajouté à la recette avec succès.');
    }

    // Modifier une association entre un ingrédient et une recette.

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantite' => 'required|numeric|min:0',
            'unite' => 'required|string',
        ]);

        $ingredientRecette = IngredientRecette::findOrFail($id);
        $ingredientRecette->update($request->only(['quantite', 'unite']));

        return redirect()->back()->with('success', 'Ingrédient mis à jour avec succès.');
    }

    
    // Supprimer une association entre un ingrédient et une recette.

    public function destroy($id)
    {
        $ingredientRecette = IngredientRecette::findOrFail($id);
        $ingredientRecette->delete();

        return redirect()->back()->with('success', 'Ingrédient retiré de la recette avec succès.');
    }
}
