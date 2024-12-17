<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeIngredient extends Model
{
    use HasFactory;

    // Attributs dans les formulaires
    protected $fillable = ['nom'];


    // Relation avec les ingrédients : Un type d'ingrédient peut avoir plusieurs ingrédients.
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'type_ingredient_id');
    }
}
