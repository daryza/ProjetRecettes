<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = ['nom'];

    /**
     * Relation : Une catégorie contient plusieurs ingrédients.
     */
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'categorie_id');
    }
}
