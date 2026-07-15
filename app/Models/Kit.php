<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Kit extends Model
{
    protected $fillable = [
        'nom_kit', 
        'description', 
        'prix_total', 
        'reduction',
        'frais_livraison', 
        'frais_carnet', 
        'prix_final', 
        'en_promotion',
        'date_debut_promo', 
        'date_fin_promo', 
        'frais_emballage',
        'frais_etiquette', 
        'kit_notes'
    ];

    protected $casts = [
        'date_debut_promo' => 'date',
        'date_fin_promo' => 'date',
        'en_promotion' => 'boolean',
    ];

    // ========== RELATIONS ==========
    
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'composition_kits')
                    ->withPivot('quantite')
                    ->withTimestamps();
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    // ========== MÉTHODES DE CALCUL ==========

    /**
     * Calculer le prix total et le prix final du kit
     */
    public function calculerPrixFinal(): void
    {
        $total = 0;
        foreach ($this->articles as $article) {
            $total += $article->prix_vente * $article->pivot->quantite;
        }

        $this->prix_total = $total;

        $prixApresReduction = $total - $this->reduction;

        $this->prix_final = $prixApresReduction + $this->frais_livraison + 
                           $this->frais_carnet + $this->frais_emballage + 
                           $this->frais_etiquette;
        
        $this->save();
    }

    /**
     * Vérifier si le kit est en promotion
     */
    public function getEstEnPromotionAttribute(): bool
    {
        if (!$this->en_promotion || !$this->date_debut_promo || !$this->date_fin_promo) {
            return false;
        }
        return Carbon::now()->between($this->date_debut_promo, $this->date_fin_promo);
    }

    /**
     * Récupérer le prix total formaté
     */
    public function getPrixTotalFormatAttribute(): string
    {
        return number_format($this->prix_total ?? 0, 0, ',', ' ') . ' F';
    }

    /**
     * Récupérer le prix final formaté
     */
    public function getPrixFinalFormatAttribute(): string
    {
        return number_format($this->prix_final ?? 0, 0, ',', ' ') . ' F';
    }

    /**
     * Récupérer la réduction formatée
     */
    public function getReductionFormatAttribute(): string
    {
        return number_format($this->reduction ?? 0, 0, ',', ' ') . ' F';
    }

    /**
     * Récupérer le nombre d'articles dans le kit
     */
    public function getNombreArticlesAttribute(): int
    {
        return $this->articles()->count();
    }

    /**
     * Récupérer le nombre total d'articles (avec les quantités)
     */
    public function getTotalQuantiteAttribute(): int
    {
        $total = 0;
        foreach ($this->articles as $article) {
            $total += $article->pivot->quantite;
        }
        return $total;
    }

    /**
     * Récupérer la liste des articles du kit
     */
    public function getListeArticlesAttribute(): string
    {
        return $this->articles->map(function($article) {
            return $article->nom_article . ' (x' . $article->pivot->quantite . ')';
        })->implode(', ');
    }

    // ========== SCOPES ==========

    /**
     * Filtrer les kits en promotion
     */
    public function scopeEnPromotion($query)
    {
        return $query->where('en_promotion', true)
                     ->where('date_debut_promo', '<=', now())
                     ->where('date_fin_promo', '>=', now());
    }

    /**
     * Filtrer les kits non en promotion
     */
    public function scopeNonEnPromotion($query)
    {
        return $query->where('en_promotion', false)
                     ->orWhere('date_debut_promo', '>', now())
                     ->orWhere('date_fin_promo', '<', now());
    }

    /**
     * Filtrer les kits par prix minimum
     */
    public function scopePrixMinimum($query, $prix)
    {
        return $query->where('prix_final', '>=', $prix);
    }

    /**
     * Filtrer les kits par prix maximum
     */
    public function scopePrixMaximum($query, $prix)
    {
        return $query->where('prix_final', '<=', $prix);
    }

    /**
     * Filtrer les kits par nom
     */
    public function scopeRechercher($query, $terme)
    {
        return $query->where('nom_kit', 'LIKE', '%' . $terme . '%')
                     ->orWhere('description', 'LIKE', '%' . $terme . '%');
    }
}