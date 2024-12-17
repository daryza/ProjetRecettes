<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recette;
use Illuminate\Http\Request;

class RecettesApiController extends Controller
{
    // Liste toutes les recettes
    public function index() {
        $recettes = Recette::with('ingredients:id,nom') // Inclure les ingrédients pour chaque recette
            ->select('id', 'nom', 'preparation', 'temps_cuisson') 
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $recettes,
        ], 200);
    }

    // Affiche une recette grace à son ID avec tous les détails
    public function show($id) {
        $recette = Recette::with('ingredients')->find($id); // Inclure les relations

        if (!$recette) {
            return response()->json(['status' => 'error', 'message' => 'Recette non trouvée'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $recette,
        ], 200);
    }

    // Crée une recette avec les ingrédients
    public function store(Request $request) {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type_recettes_id' => 'required|exists:type_recettes,id',
            'preparation' => 'required|string',
            'temps_cuisson' => 'nullable|integer',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantite' => 'required|numeric|min:0',
            'ingredients.*.unite' => 'required|string|max:10',
        ]);

        // Créer la recette
        $recette = Recette::create([
            'nom' => $validated['nom'],
            'type_recettes_id' => $validated['type_recettes_id'],
            'preparation' => $validated['preparation'],
            'temps_cuisson' => $validated['temps_cuisson'],
            'users_id' => $request->user()->id, // Utilisateur authentifié
        ]);

        // Attacher les ingrédients
        foreach ($validated['ingredients'] as $ingredient) {
            $recette->ingredients()->attach($ingredient['id'], [
                'quantite' => $ingredient['quantite'],
                'unite' => $ingredient['unite'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $recette->load('ingredients'), // Inclure les relations après création
        ], 201);
    }

    // Met à jour une recette existante avec ses ingrédients
    public function update(Request $request, $id) {
        $recette = Recette::find($id);

        if (!$recette) {
            return response()->json(['status' => 'error', 'message' => 'Recette non trouvée'], 404);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'type_recettes_id' => 'sometimes|exists:type_recettes,id',
            'preparation' => 'sometimes|string',
            'temps_cuisson' => 'nullable|integer',
            'ingredients' => 'nullable|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantite' => 'required|numeric|min:0',
            'ingredients.*.unite' => 'required|string|max:10',
        ]);

        // Mise à jour des champs de la recette
        $recette->update($validated);

        // Mise à jour des ingrédients si fournis
        if (isset($validated['ingredients'])) {
            $ingredientsData = [];
            foreach ($validated['ingredients'] as $ingredient) {
                $ingredientsData[$ingredient['id']] = [
                    'quantite' => $ingredient['quantite'],
                    'unite' => $ingredient['unite'],
                ];
            }
            // Remplacer detach + attach par sync
            $recette->ingredients()->sync($ingredientsData);
        }
        

        return response()->json([
            'status' => 'success',
            'data' => $recette->load('ingredients'), // Inclure les relations après mise à jour
        ], 200);
    }

    // Supprime une recette
    public function destroy($id) {
        $recette = Recette::find($id);

        if (!$recette) {
            return response()->json(['status' => 'error', 'message' => 'Recette non trouvée'], 404);
        }

        // Détacher les ingrédients avant suppression
        $recette->ingredients()->detach();
        $recette->delete();

        return response()->json(['status' => 'success', 'message' => 'Recette supprimée'], 200);
    }
}
