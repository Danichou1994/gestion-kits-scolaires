<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Echeance extends Model
{
    protected $fillable = [
        'vente_id', 'client_id', 'date_echeance',
        'montant_dû', 'statut', 'date_paiement'
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'date_paiement' => 'date',
    ];

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function estEnRetard(): bool
    {
        return $this->statut === 'en_attente' && $this->date_echeance < now();
    }
}