<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    // Attributs dans les formulaires
    protected $fillable = [
        'pseudo',          // Pseudo de l'utilisateur
        'password',    // Mot de passe de l'utilisateur
        'role',            // Rôle de l'utilisateur
    ];


    // Relation 1-N : Un utilisateur peut avoir plusieurs recettes
    public function recettes()
    {
    return $this->hasMany(Recette::class, 'users_id'); // Vérifiez que la clé étrangère est bien 'users_id' dans la table 'recettes'
    }
    

    // Vérifie si l'utilisateur est admin.

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    
    // Vérifie si l'utilisateur est invité

    public function isGuest(): bool
    {
        return $this->role === 'guest';
    }


    // Vérifie si l'utilisateur est un utilisateur inscrit (par défaut).

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
