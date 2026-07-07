<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'nom_article', 'code_barre', 'prix_achat', 'prix_vente',
        'prix_unitaire', 'benefice', 'categorie', 'fournisseur',
        'unite_mesure', 'emplacement', 'stock', 'seuil_alerte'
    ];

    public function kits(): BelongsToMany
    {
        return $this->belongsToMany(Kit::class, 'composition_kits')->withPivot('quantite');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function getBeneficeTotalAttribute(): float
    {
        return $this->benefice * $this->stock;
    }
}