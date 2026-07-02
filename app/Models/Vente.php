<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vente extends Model
{
    protected $fillable = [
        'client_id',
        'kit_id',
        'montant_total',
        'acompte',
        'solde',
        'nb_mensualites',
        'montant_mensualite',
        'statut',
        'date_vente'
    ];

    protected $casts = [
        'date_vente' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function kit(): BelongsTo
    {
        return $this->belongsTo(Kit::class);
    }

    public function echeances(): HasMany
    {
        return $this->hasMany(Echeance::class);
    }
}