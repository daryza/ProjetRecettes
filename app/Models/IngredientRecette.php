<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class IngredientRecette extends Pivot
{
    protected $table = 'ingredient_recette';

    protected $fillable = [
        'recette_id',
        'ingredient_id',
        'quantite',
        'unite',
    ];
}
