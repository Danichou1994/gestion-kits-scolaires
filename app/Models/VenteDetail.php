<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenteDetail extends Model
{
    protected $fillable = [
        'vente_id', 'type', 'article_id', 'kit_id',
        'quantite', 'prix_unitaire', 'montant_ht',
        'tva', 'total_ligne'
    ];

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function kit(): BelongsTo
    {
        return $this->belongsTo(Kit::class);
    }

    public function getNomProduitAttribute(): string
    {
        if ($this->type === 'article') {
            return $this->article->nom_article ?? 'N/A';
        }
        return $this->kit->nom_kit ?? 'N/A';
    }
}