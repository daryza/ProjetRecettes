<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeRecette extends Model
{
    use HasFactory;

    // Attributs via les formulaires
    protected $fillable = ['nom'];

    
    // Relation avec les recettes : Un type de recette peut avoir plusieurs recettes.
    
    public function recettes()
    {
        return $this->hasMany(Recette::class, 'type_recettes_id');
    }
}
