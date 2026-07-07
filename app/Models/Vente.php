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
        'montant_mensualite', 'statut', 'mode_paiement', 'date_vente'
    ];

    protected $casts = ['date_vente' => 'date'];

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
}