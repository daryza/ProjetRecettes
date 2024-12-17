<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IngredientsController extends Controller
{
    public function store(Request $request)
    {
        // Valider les données entrantes
        $request->validate([
            'nom' => 'required|string|max:255',
            'type_ingredient_id' => 'required|exists:type_ingredients,id',
        ]);

        $nom = $request->input('nom');

        // Vérifier si un ingrédient similaire existe déjà
        $existing = Ingredient::whereRaw('LOWER(nom) = ?', [strtolower($nom)])
            ->orWhereRaw('LOWER(nom) = ?', [strtolower(Str::singular($nom))])
            ->orWhereRaw('LOWER(nom) = ?', [strtolower(Str::plural($nom))])
            ->first();

        if ($existing) {
            return redirect()->back()->withErrors('Un ingrédient similaire existe déjà.');
        }

        // Créer l'ingrédient
        Ingredient::create([
            'nom' => $nom,
            'type_ingredient_id' => $request->type_ingredient_id,
        ]);

        return redirect()->back()->with('success', 'Ingrédient ajouté avec succès.');
    }

    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();

        return redirect()->back()->with('success', 'Ingrédient supprimé.');
    }
}
