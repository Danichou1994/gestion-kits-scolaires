<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Kit extends Model
{
    protected $fillable = [
        'nom_kit', 'description', 'prix_total', 'reduction',
        'frais_livraison', 'frais_carnet', 'prix_final', 'en_promotion',
        'date_debut_promo', 'date_fin_promo', 'frais_emballage',
        'frais_etiquette', 'kit_notes'
    ];

    protected $casts = [
        'date_debut_promo' => 'date',
        'date_fin_promo' => 'date',
        'en_promotion' => 'boolean',
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

        $estEnPromo = $this->en_promotion && 
                      $this->date_debut_promo && 
                      $this->date_fin_promo && 
                      Carbon::now()->between($this->date_debut_promo, $this->date_fin_promo);

        $prixApresReduction = $total - $this->reduction;

        $this->prix_final = $prixApresReduction + $this->frais_livraison + 
                           $this->frais_carnet + $this->frais_emballage + 
                           $this->frais_etiquette;
        $this->save();
    }

    public function getEstEnPromotionAttribute(): bool
    {
        if (!$this->en_promotion || !$this->date_debut_promo || !$this->date_fin_promo) {
            return false;
        }
        return Carbon::now()->between($this->date_debut_promo, $this->date_fin_promo);
    }

    public function getPrixFinalFormatAttribute(): string
    {
        return number_format($this->prix_final, 0, ',', ' ') . ' F';
    }
}