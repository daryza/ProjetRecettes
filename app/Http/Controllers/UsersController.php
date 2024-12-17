<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    // Gérer la connexion
    public function connexion(Request $request)
    {
        $credentials = $request->validate([
            'pseudo' => 'required|string',
            'mot_de_passe' => 'required|string',
        ]);

        if (Auth::attempt(['pseudo' => $credentials['pseudo'], 'password' => $credentials['mot_de_passe']])) {
            return redirect()->route('accueil')->with('success', 'Connexion réussie.');
        }

        return back()->withErrors([
            'pseudo' => 'Les informations de connexion sont incorrectes.',
        ]);
    }

    // Gérer l'inscription
    public function inscription(Request $request)
    {
        $request->validate([
            'pseudo' => 'required|string|unique:users|max:255',
            'mot_de_passe' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'pseudo' => $request->pseudo,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
        ]);

        return redirect()->route('connexion')->with('success', 'Compte créé avec succès.');
    }

    // Déconnexion
    public function deconnexion()
    {
        Auth::logout();
        return redirect()->route('accueil');
    }

    public function destroy($id)
{
    // Récupérer l'utilisateur
    $user = User::findOrFail($id);

    // Vérification des permissions
    if (Auth::id() !== $user->id && !Auth::user()->isAdmin()) {
        return redirect()->route('accueil')->with('error', 'Vous n\'êtes pas autorisé à supprimer cet utilisateur.');
    }

    // Supprimer l'utilisateur
    $user->delete();

    // Rediriger avec un message de succès
    return redirect()->route('accueil')->with('success', 'Utilisateur supprimé avec succès.');
}


    // Modèle User relation recettes
    public function recettes()
    {
        return $this->hasMany(Recette::class);
    }


}
