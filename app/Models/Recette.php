<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recette extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',            // Nom de la recette
        'preparation',    // Étapes de préparation
        'temps_cuisson',  // Temps de cuisson
        'users_id',       // ID de l'utilisateur (créateur)
        'type_recettes_id' // ID du type de recette
    ];

    
    // Relation N-1 : Une recette appartient à un utilisateur.
    
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    
    // Relation N-N : Une recette a plusieurs ingrédients.
    
    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_recette', // Table pivot
            'recette_id',         // Clé étrangère vers Recette
            'ingredient_id'       // Clé étrangère vers Ingrédient
        )->withPivot('quantite', 'unite')->withTimestamps();
    }

    
    // Relation N-1 : Une recette a un type.
    
    public function typeRecette()
{
    return $this->belongsTo(TypeRecette::class, 'type_recettes_id');
}

}
