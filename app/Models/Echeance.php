<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Echeance extends Model
{
    // ========== CONSTANTES DE STATUT ==========
    const STATUT_PAYE = 'paye';
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_EN_RETARD = 'en_retard';

    protected $fillable = [
        'vente_id',
        'client_id',
        'date_echeance',
        'montant_dû',
        'statut',
        'date_paiement',
        'notes',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'date_paiement' => 'date',
    ];

    // ========== RELATIONS ==========

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // ========== SCOPES ==========

    public function scopePaye($query)
    {
        return $query->where('statut', self::STATUT_PAYE);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', self::STATUT_EN_ATTENTE);
    }

    public function scopeEnRetard($query)
    {
        return $query->where('statut', self::STATUT_EN_RETARD);
    }

    // ========== ATTRIBUTS ==========

    public function getMontantDuFormatAttribute()
    {
        return number_format($this->montant_dû, 0, ',', ' ') . ' F';
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            self::STATUT_PAYE => '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✅ Payé</span>',
            self::STATUT_EN_ATTENTE => '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">⏳ En attente</span>',
            self::STATUT_EN_RETARD => '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">⚠️ En retard</span>',
        ];
        return $badges[$this->statut] ?? $badges[self::STATUT_EN_ATTENTE];
    }

    public function getStatutLabelAttribute()
    {
        $labels = [
            self::STATUT_PAYE => 'Payé',
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_EN_RETARD => 'En retard',
        ];
        return $labels[$this->statut] ?? 'En attente';
    }
}