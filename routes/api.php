<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecettesApiController;

Route::get('/recettes', [RecettesApiController::class, 'index']); // Liste toutes les recettes
Route::get('/recettes/{id}', [RecettesApiController::class, 'show']); // Affiche une recette
Route::post('/recettes', [RecettesApiController::class, 'store']); // Crée une nouvelle recette
Route::put('/recettes/{id}', [RecettesApiController::class, 'update']); // Met à jour une recette
Route::delete('/recettes/{id}', [RecettesApiController::class, 'destroy']); // Supprime une recette
