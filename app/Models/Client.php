<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 'adresse', 'quartier'
    ];

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function echeances(): HasMany
    {
        return $this->hasMany(Echeance::class);
    }

    public function peutEtreSupprime(): bool
    {
        return $this->echeances()->where('statut', '!=', 'paye')->count() === 0;
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}