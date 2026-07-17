<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'nom_article',
        'code_barre',
        'prix_unitaire',
        'prix_achat',
        'prix_vente',
        'benefice',
        'categorie',
        'fournisseur',
        'unite_mesure',
        'stock',
        'seuil_alerte',
        'active',
        'emplacement',
        'description',
        'marque',
        'poids',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ========== RELATIONS ==========

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function kits()
    {
        return $this->belongsToMany(Kit::class, 'composition_kits')
                    ->withPivot('quantite')
                    ->withTimestamps();
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    // ========== ATTRIBUTS ==========

    public function getBeneficeTotalAttribute()
    {
        return $this->benefice * $this->stock;
    }

    // ========== SCOPES ==========

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    public function scopeCategorie($query, $categorie)
    {
        return $query->where('categorie', $categorie);
    }

    public function scopeStockBas($query)
    {
        return $query->whereColumn('stock', '<=', 'seuil_alerte');
    }

    public function scopeRechercher($query, $terme)
    {
        return $query->where('nom_article', 'LIKE', '%' . $terme . '%')
                     ->orWhere('code_barre', 'LIKE', '%' . $terme . '%');
    }
}