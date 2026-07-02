<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kit extends Model
{
    protected $fillable = [
        'nom_kit',
        'description',
        'prix_total'
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'composition_kits')
                    ->withPivot('quantite');
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }
}