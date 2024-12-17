<?php

namespace App\Http\Controllers;

use App\Models\Recette;

class AccueilController extends Controller
{

    public function index()
    {
        // Récupération des données :
        $recetteDuMois = Recette::inRandomOrder()->first(); // Affiche suggestion du chef 
        $dernieresRecettes = Recette::latest()->take(5)->get(); // Affiche les 5 dernières recettes

        return view('accueil', [
            'recetteDuMois' => $recetteDuMois,
            'dernieresRecettes' => $dernieresRecettes,
        ]);
    }
}
