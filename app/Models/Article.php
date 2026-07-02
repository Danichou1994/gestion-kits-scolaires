<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    protected $fillable = [
        'nom_article',
        'prix_unitaire',
        'categorie',
        'stock',
        'seuil_alerte'
    ];

    public function kits(): BelongsToMany
    {
        return $this->belongsToMany(Kit::class, 'composition_kits')
                    ->withPivot('quantite');
    }
}