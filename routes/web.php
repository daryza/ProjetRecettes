<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecettesController;
use App\Http\Controllers\IngredientsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IngredientRecettesController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\Api\RecettesApiController; // Import du contrôleur API
use App\Models\TypeRecette;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Ici, vous pouvez enregistrer les routes web de votre application.
| Ces routes sont chargées par le RouteServiceProvider dans un groupe
| qui contient le groupe de middleware "web".
*/

// Page d'accueil
Route::get('/', [AccueilController::class, 'index'])->name('accueil');

// Routes publiques
Route::get('recettes/create', [RecettesController::class, 'create'])->name('recettes.create');
Route::get('recettes/mes-recettes', [RecettesController::class, 'mesRecettes'])->name('recettes.mes_recettes');
Route::get('recettes', [RecettesController::class, 'index'])->name('recettes.index');
Route::get('recettes/{recette}', [RecettesController::class, 'show'])->name('recettes.show');

// Edition et mise à jour des recettes
Route::get('recettes/{recette}/edit', [RecettesController::class, 'edit'])->name('recettes.edit');
Route::put('recettes/{recette}', [RecettesController::class, 'update'])->name('recettes.update');

// Routes d'authentification
Route::get('inscription', [AuthController::class, 'showRegistrationForm'])->name('inscription');
Route::post('inscription', [AuthController::class, 'register']);
Route::get('connexion', [AuthController::class, 'showLoginForm'])->name('connexion');
Route::post('connexion', [AuthController::class, 'login']);
Route::post('deconnexion', [AuthController::class, 'logout'])->name('deconnexion');

// Route de test : /test-flash
// Route::get('/test-flash', function () {
    // Flash d'un message de succès
//    session()->flash('success', 'Test de session flash dans une route.');

    // Récupération des données nécessaires pour la vue
//    $typesRecettes = TypeRecette::all(); // Récupère tous les types de recettes
//    $categories = []; // Exemple vide ou ajustez avec des données réelles si nécessaire
//    $listeCategories = TypeRecette::all(); // Ajout pour éviter les erreurs de variable manquante

//    return view('recettes.create', compact('typesRecettes', 'categories', 'listeCategories'));
// });

// Téléchargement PDF
Route::get('/recettes/{id}/pdf', [RecettesController::class, 'telechargerPDF'])->name('recettes.pdf');

// Routes protégées par le middleware 'auth'
Route::middleware(['auth'])->group(function () {
    // Gestion des recettes
    Route::post('recettes', [RecettesController::class, 'store'])->name('recettes.store');
    Route::put('recettes/{recette}', [RecettesController::class, 'update'])->name('recettes.update');
    Route::delete('recettes/{recette}', [RecettesController::class, 'destroy'])->name('recettes.destroy');

    // Gestion des ingrédients
    Route::post('ingredients', [IngredientsController::class, 'store'])->name('ingredients.store');

    // Gestion des relations ingrédient-recette
    Route::post('ingredient-recettes', [IngredientRecettesController::class, 'store'])->name('ingredient-recettes.store');
    Route::put('ingredient-recettes/{id}', [IngredientRecettesController::class, 'update'])->name('ingredient-recettes.update');
    Route::delete('ingredient-recettes/{id}', [IngredientRecettesController::class, 'destroy'])->name('ingredient-recettes.destroy');

    // Gestion des utilisateurs
    Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
});

    Route::prefix('api')->group(function () {
        Route::get('/recettes', [RecettesApiController::class, 'index']); // Liste toutes les recettes
        Route::get('/recettes/{id}', [RecettesApiController::class, 'show']); // Affiche une recette
        Route::post('/recettes', [RecettesApiController::class, 'store']); // Crée une nouvelle recette
        Route::put('/recettes/{id}', [RecettesApiController::class, 'update']); // Met à jour une recette
        Route::delete('/recettes/{id}', [RecettesApiController::class, 'destroy']); // Supprime une recette
});



