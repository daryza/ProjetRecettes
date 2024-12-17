<?php

namespace App\Http\Controllers;

use App\Models\Recette;
use App\Models\Ingredient;
use App\Models\TypeRecette;
use App\Models\TypeIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class RecettesController extends Controller
{
    // Afficher la liste de toutes les recettes
    public function index()
    {
        Log::info('Méthode index appelée');
        $recettes = Recette::all(); // Récupère toutes les recettes
        Log::info('Recettes récupérées pour index : ' . json_encode($recettes->toArray()));
        return view('recettes.index', compact('recettes'));
    }

    // Afficher une recette spécifique
    public function show($id)
    {
        Log::info('Méthode show appelée avec ID : ' . $id);
        $recette = Recette::findOrFail($id); // Trouve la recette ou retourne une erreur 404
        Log::info('Recette affichée : ' . json_encode($recette->toArray()));
        return view('recettes.show', compact('recette'));
    }

    // Afficher le formulaire de création de recette
    public function create()
    {
        Log::info('Méthode create appelée');

        // Charger les types de recettes
        $typesRecettes = TypeRecette::all();
        Log::info('Types de recettes récupérés : ' . json_encode($typesRecettes->toArray()));

        // Charger les ingrédients groupés par type
        $categories = Ingredient::with('typeIngredient')->get()->groupBy('typeIngredient.nom');
        Log::info('Ingrédients récupérés et groupés par type : ' . json_encode($categories));

        // Charger les types d'ingrédients
        $listeCategories = \App\Models\TypeIngredient::all();
        Log::info('Types d\'ingrédients récupérés : ' . json_encode($listeCategories->toArray()));

        return view('recettes.create', compact('categories', 'typesRecettes', 'listeCategories'));
    }

    // Enregistrer une nouvelle recette
    public function store(Request $request)
    {
        Log::info('Méthode store appelée avec données : ' . json_encode($request->all()));

        // Validation des données sans messages personnalisés
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type_recettes_id' => 'required|exists:type_recettes,id',
            'preparation' => 'required|string',
            'temps_cuisson' => 'nullable|integer',
            'ingredients' => 'required|array',
            'ingredients_quantites' => 'required|array',
            'ingredients_unites' => 'required|array',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        Log::info('Validation réussie pour les données : ' . json_encode($validated));

        try {
            // Création de la recette
            $recette = Recette::create([
                'nom' => $validated['nom'],
                'type_recettes_id' => $validated['type_recettes_id'],
                'preparation' => $validated['preparation'],
                'temps_cuisson' => $validated['temps_cuisson'],
                'users_id' => Auth::id(),
            ]);
            Log::info('Nouvelle recette créée : ' . json_encode($recette->toArray()));

            // Gestion de la photo
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('recettes', 'public');
                $recette->photo = $photoPath;
                $recette->save();
                Log::info('Photo ajoutée à la recette : ' . $photoPath);
            }

            // Ajout des ingrédients
            foreach ($validated['ingredients'] as $index => $ingredientId) {
                $recette->ingredients()->attach($ingredientId, [
                    'quantite' => $validated['ingredients_quantites'][$index],
                    'unite' => $validated['ingredients_unites'][$index],
                ]);
            }
            Log::info('Ingrédients ajoutés à la recette : ' . json_encode($recette->ingredients->toArray()));

            // Ajout d'un message de succès
            session()->flash('success', 'La recette "' . $recette->nom . '" a été créée avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la recette : ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors de la création de la recette.');
            return redirect()->route('recettes.create')->withInput();
        }

        return redirect()->route('recettes.index');
    }

    // Modifier une recette existante
    public function update(Request $request, $id) 
{
    Log::info('Méthode update appelée avec ID : ' . $id . ' et données : ' . json_encode($request->all()));
    $recette = Recette::findOrFail($id);

    // Vérification des permissions
    if ($recette->users_id !== Auth::id() && !Auth::user()->isAdmin()) {
        Log::warning('Utilisateur non autorisé à modifier la recette.');
        return redirect()->route('recettes.index')->with('error', 'Vous n\'êtes pas autorisé à modifier cette recette.');
    }

    // Validation des données
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'type_recettes_id' => 'required|exists:type_recettes,id',
        'preparation' => 'required|string',
        'temps_cuisson' => 'nullable|integer',
        'ingredients' => 'required|array',
        'ingredients_quantites' => 'required|array',
        'ingredients_unites' => 'required|array',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);
    Log::info('Validation réussie pour la mise à jour.');

    // Gestion de la photo
    if ($request->hasFile('photo')) {
        if ($recette->photo) {
            \Storage::disk('public')->delete($recette->photo);
        }
        $photoPath = $request->file('photo')->store('recettes', 'public');
        $recette->photo = $photoPath;
        Log::info('Photo mise à jour : ' . $photoPath);
    }

    // Mise à jour de la recette
    $recette->update($validated);
    Log::info('Recette mise à jour : ' . json_encode($recette->toArray()));

    // Mise à jour des ingrédients avec sync()
    $ingredientsData = [];
    foreach ($validated['ingredients'] as $index => $ingredientId) {
        $ingredientsData[$ingredientId] = [
            'quantite' => $validated['ingredients_quantites'][$index],
            'unite' => $validated['ingredients_unites'][$index],
        ];
    }
    $recette->ingredients()->sync($ingredientsData);
    Log::info('Ingrédients mis à jour pour la recette avec sync.');

    return redirect()->route('recettes.show', $recette->id)->with('success', 'Recette mise à jour avec succès.');
}
    // Supprimer une recette
    public function destroy($id)
    {
        Log::info('Méthode destroy appelée avec ID : ' . $id);
        $recette = Recette::findOrFail($id);

        if ($recette->users_id !== Auth::id() && !Auth::user()->isAdmin()) {
            Log::warning('Utilisateur non autorisé à supprimer la recette.');
            return redirect()->route('recettes.index')->with('error', 'Vous n\'êtes pas autorisé à supprimer cette recette.');
        }

        if ($recette->photo) {
            \Storage::disk('public')->delete($recette->photo);
        }

        $recette->delete();
        Log::info('Recette supprimée : ' . $id);

        return redirect()->route('recettes.index')->with('success', 'Recette supprimée avec succès.');
    }

    // Afficher les recettes de l'utilisateur connecté
    public function mesRecettes()
    {
        Log::info('Méthode mesRecettes appelée');
        $user = auth()->user();
        $recettes = $user->recettes;
        Log::info('Recettes de l\'utilisateur récupérées : ' . json_encode($recettes->toArray()));
        return view('recettes.mes_recettes', compact('recettes'));
    }

    public function telechargerPDF($id)
    {
        // Récupérer la recette par son ID
        $recette = Recette::with('ingredients')->findOrFail($id);

        // Passer les données à une vue spécifique pour générer le PDF
        $pdf = Pdf::loadView('recettes.pdf', compact('recette'));

        // Télécharger le fichier PDF
        return $pdf->download('recette_' . $recette->nom . '.pdf');
    }

    // Modifier une recette
public function edit($id)
{
    Log::info('Méthode edit appelée avec ID : ' . $id);

    // Trouver la recette avec ses ingrédients associés
    $recette = Recette::with('ingredients')->findOrFail($id);
    Log::info('Recette trouvée pour modification : ' . json_encode($recette->toArray()));

    // Charger les types de recettes et les ingrédients par catégorie
    $typesRecettes = TypeRecette::all();
    Log::info('Types de recettes récupérés : ' . json_encode($typesRecettes->toArray()));

    $categories = Ingredient::with('typeIngredient')->get()->groupBy('typeIngredient.nom');
    Log::info('Ingrédients récupérés et groupés par type : ' . json_encode($categories));

    return view('recettes.edit', compact('recette', 'typesRecettes', 'categories'));
}

}
