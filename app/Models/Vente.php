<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vente extends Model
{
    protected $fillable = [
        'numero_vente', 'client_id', 'type_vente', 'kit_id', 'article_id',
        'quantite', 'montant_total', 'remise', 'frais_livraison',
        'frais_carnet', 'acompte', 'solde', 'nb_mensualites',
        'montant_mensualite', 'statut', 'mode_paiement', 'date_vente',
        'reference_paiement', 'notes'
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

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function echeances(): HasMany
    {
        return $this->hasMany(Echeance::class);
    }

    public static function genererNumero(): string
    {
        $last = self::orderBy('id', 'desc')->first();
        $num = $last ? intval(substr($last->numero_vente, -5)) + 1 : 1;
        return 'FV-' . date('Ymd') . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }

    public function getMontantTotalFormateAttribute(): string
    {
        return number_format($this->montant_total, 0, ',', ' ') . ' F';
    }

    public function getStatutBadgeAttribute(): string
    {
        $badges = [
            'en_cours' => '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">⏳ En cours</span>',
            'termine' => '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✅ Terminé</span>',
            'annule' => '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">❌ Annulé</span>'
        ];
        return $badges[$this->statut] ?? $badges['en_cours'];
    }

    public function getTypeVenteLabelAttribute(): string
    {
        return $this->type_vente == 'kit' ? '🎒 Kit' : '📦 Article';
    }
}