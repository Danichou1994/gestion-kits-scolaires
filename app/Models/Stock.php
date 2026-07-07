<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'article_id', 'type', 'quantite', 'prix_unitaire',
        'vente_id', 'reference', 'motif', 'stock_avant',
        'stock_apres', 'date_mouvement'
    ];

    protected $casts = ['date_mouvement' => 'date'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class);
    }
}