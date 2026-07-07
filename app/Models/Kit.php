<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kit extends Model
{
    protected $fillable = [
        'nom_kit', 'description', 'prix_total', 'reduction',
        'frais_livraison', 'frais_carnet', 'prix_final', 'en_promotion'
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'composition_kits')->withPivot('quantite');
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function calculerPrixFinal(): void
    {
        $total = 0;
        foreach ($this->articles as $article) {
            $total += $article->prix_vente * $article->pivot->quantite;
        }
        
        $this->prix_total = $total;
        $this->prix_final = $total - $this->reduction + $this->frais_livraison + $this->frais_carnet;
        $this->save();
    }
}