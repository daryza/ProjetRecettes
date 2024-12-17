<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth.connexion'); // Affiche la page de connexion et d'inscription
    }

    
     // Inscription d'un utilisateur.

    public function register(Request $request)
    {
        // Validation des données d'inscription et messages erreur
        $request->validate([
            'pseudo' => 'required|unique:users,pseudo|max:255',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.min' => 'Mot de passe trop court. Il doit contenir au moins 6 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'pseudo.unique' => 'Ce pseudo est déjà pris.',
        ]);

        // Création de l'utilisateur
        $user = User::create([
            'pseudo' => $request->pseudo,
            'password' => bcrypt($request->password),
        ]);

        // Connexion automatique de l'utilisateur
        Auth::login($user);

        // Redirection vers la page d'accueil avec un message
        return redirect()->route('accueil')->with('success', 'Création de compte réussie. Vous êtes maintenant connecté.');
    }

    
     // Afficher le formulaire de connexion
     
    public function showLoginForm()
    {
        return view('auth.connexion');
    }

     // Connexion de l'utilisateur
    
    public function login(Request $request)
    {
        $credentials = $request->only('pseudo', 'password');

        if (Auth::attempt(['pseudo' => $credentials['pseudo'], 'password' => $credentials['password']])) {
            return redirect()->route('accueil')->with('success', 'Connexion réussie.');
        }

        return back()->withErrors(['pseudo' => 'Les informations de connexion sont incorrectes.']);
    }


     // Déconnexion de l'utilisateur
    public function logout()
    {
        Auth::logout();
        return redirect()->route('connexion')->with('success', 'Déconnexion réussie.');
    }
}

