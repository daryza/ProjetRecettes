<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'type_ingredient_id'];

    
     // Relation N-1 : Un ingrédient appartient à un seul type d'ingrédient.
    
    public function typeIngredient()
    {
        return $this->belongsTo(TypeIngredient::class, 'type_ingredient_id');
    }


    // Relation N-N : Un ingrédient peut appartenir à plusieurs recettes.

    public function recettes()
    {
        return $this->belongsToMany(
            Recette::class,        // Modèle lié
            'ingredient_recette',  // Nom de la table pivot
            'ingredient_id',       // Clé étrangère dans la table pivot pour l'ingrédient
            'recette_id'           // Clé étrangère dans la table pivot pour la recette
        )->withPivot('quantite', 'unite')->withTimestamps();
    }
}

