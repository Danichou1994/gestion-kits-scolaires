<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture #{{ $vente->numero_vente }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            padding: 20px;
            font-size: 12px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
            background: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header-left h1 {
            color: #2563EB;
            font-size: 24px;
        }
        .header-left p {
            color: #64748b;
            font-size: 12px;
            margin: 2px 0;
        }
        .header-right {
            text-align: right;
        }
        .header-right h2 {
            color: #1e293b;
            font-size: 22px;
        }
        .header-right .facture-num {
            color: #2563EB;
            font-weight: bold;
            font-size: 14px;
        }
        .info-client {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-client h3 { color: #1e293b; margin-bottom: 5px; }
        .info-client p { color: #475569; font-size: 12px; margin: 2px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #2563EB;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .total-row {
            background: #f1f5f9;
            font-weight: bold;
        }
        .montant { text-align: right; }
        .recap {
            margin-top: 20px;
            padding: 15px;
            background: #f1f5f9;
            border-radius: 8px;
        }
        .recap-item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .recap-item.total {
            border-top: 2px solid #2563EB;
            padding-top: 10px;
            margin-top: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 11px;
        }
        .statut-paye { color: #16a34a; font-weight: bold; }
        .statut-attente { color: #d97706; font-weight: bold; }
        .statut-retard { color: #dc2626; font-weight: bold; }
        .badge-promo {
            background: #dc2626;
            color: white;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- En-tête avec établissement -->
        <div class="header">
            <div class="header-left">
                <h1>📚 {{ config('etablissement.nom') }}</h1>
                <p>{{ config('etablissement.adresse') }}</p>
                <p>📞 {{ config('etablissement.telephone') }}</p>
                <p>✉️ {{ config('etablissement.email') }}</p>
            </div>
            <div class="header-right">
                <h2>FACTURE</h2>
                <p class="facture-num">N° {{ $vente->numero_vente }}</p>
                <p>Date: {{ $vente->date_vente->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Client -->
        <div class="info-client">
            <h3>👤 Client</h3>
            <p><strong>{{ $vente->client->prenom }} {{ $vente->client->nom }}</strong></p>
            <p>📞 {{ $vente->client->telephone }}</p>
            @if($vente->client->adresse)
                <p>📍 {{ $vente->client->adresse }}{{ $vente->client->quartier ? ' - '.$vente->client->quartier : '' }}</p>
            @endif
        </div>

        <!-- Détails des articles -->
        <h3>📦 Détails de la commande</h3>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Prix unit.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalItems = 0; @endphp
                @if($vente->items)
                    @foreach(json_decode($vente->items, true) as $item)
                    <tr>
                        <td>{{ $item['nom'] }} @if($item['type'] == 'kit') <span class="badge-promo">Kit</span> @endif</td>
                        <td>{{ $item['quantite'] }}</td>
                        <td class="montant">{{ number_format($item['prix'], 0, ',', ' ') }} F</td>
                        <td class="montant">{{ number_format($item['total'], 0, ',', ' ') }} F</td>
                    </tr>
                    @php $totalItems += $item['total']; @endphp
                    @endforeach
                @else
                    @if($vente->type_vente == 'kit' && $vente->kit)
                        @foreach($vente->kit->articles as $article)
                        <tr>
                            <td>{{ $article->nom_article }}</td>
                            <td>{{ $article->pivot->quantite }}</td>
                            <td class="montant">{{ number_format($article->prix_vente, 0, ',', ' ') }} F</td>
                            <td class="montant">{{ number_format($article->prix_vente * $article->pivot->quantite, 0, ',', ' ') }} F</td>
                        </tr>
                        @php $totalItems += $article->prix_vente * $article->pivot->quantite; @endphp
                        @endforeach
                    @elseif($vente->article)
                        <tr>
                            <td>{{ $vente->article->nom_article }}</td>
                            <td>{{ $vente->quantite }}</td>
                            <td class="montant">{{ number_format($vente->article->prix_vente, 0, ',', ' ') }} F</td>
                            <td class="montant">{{ number_format($vente->article->prix_vente * $vente->quantite, 0, ',', ' ') }} F</td>
                        </tr>
                        @php $totalItems += $vente->article->prix_vente * $vente->quantite; @endphp
                    @endif
                @endif
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Sous-total</td>
                    <td class="montant">{{ number_format($totalItems, 0, ',', ' ') }} F</td>
                </tr>
            </tbody>
        </table>

        <!-- Récapitulatif (sans TVA) -->
        <div class="recap">
            <div class="recap-item"><span>💰 Sous-total</span><span>{{ number_format($totalItems, 0, ',', ' ') }} F</span></div>
            @if(($vente->remise ?? 0) > 0)
            <div class="recap-item"><span>💳 Remise</span><span>-{{ number_format($vente->remise ?? 0, 0, ',', ' ') }} F</span></div>
            @endif
            @if(($vente->frais_livraison ?? 0) > 0)
            <div class="recap-item"><span>🚚 Frais livraison</span><span>{{ number_format($vente->frais_livraison ?? 0, 0, ',', ' ') }} F</span></div>
            @endif
            @if(($vente->frais_carnet ?? 0) > 0)
            <div class="recap-item"><span>📋 Frais carnet</span><span>{{ number_format($vente->frais_carnet ?? 0, 0, ',', ' ') }} F</span></div>
            @endif
            <div class="recap-item total">
                <span>💰 TOTAL</span>
                <span>{{ number_format($vente->montant_total, 0, ',', ' ') }} F</span>
            </div>
        </div>

        <!-- Paiement -->
        <div class="recap" style="margin-top:10px; background:#eff6ff;">
            <div class="recap-item"><span>💳 Acompte (1/4)</span><span>{{ number_format($vente->acompte, 0, ',', ' ') }} F</span></div>
            <div class="recap-item"><span>📆 Solde restant</span><span>{{ number_format($vente->solde, 0, ',', ' ') }} F</span></div>
            <div class="recap-item"><span>📊 Mensualités</span><span>{{ $vente->nb_mensualites }} x {{ number_format($vente->montant_mensualite, 0, ',', ' ') }} F</span></div>
            <div class="recap-item total">
                <span>STATUT</span>
                <span>
                    @if($vente->statut == 'en_cours')
                        <span class="statut-attente">⏳ En cours de paiement</span>
                    @elseif($vente->statut == 'termine')
                        <span class="statut-paye">✅ Payé</span>
                    @else
                        <span class="statut-retard">⚠️ Annulé</span>
                    @endif
                </span>
            </div>
        </div>

        <!-- Échéances -->
        @if($vente->echeances->count() > 0)
        <div style="margin-top:15px; padding:15px; border:1px solid #dbeafe; background:#eff6ff; border-radius:8px;">
            <h4>📅 Échéances</h4>
            <table style="margin:10px 0;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vente->echeances as $echeance)
                    <tr>
                        <td>{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                        <td class="montant">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                        <td>
                            @if($echeance->statut == 'paye')
                                <span class="statut-paye">Payé</span>
                            @elseif($echeance->statut == 'en_attente')
                                <span class="statut-attente">En attente</span>
                            @else
                                <span class="statut-retard">En retard</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Pied de page -->
        <div class="footer">
            <p>Merci pour votre confiance !</p>
            <p>{{ config('etablissement.nom') }} - {{ config('etablissement.adresse') }}</p>
            <p>📞 {{ config('etablissement.telephone') }} | ✉️ {{ config('etablissement.email') }}</p>
            <p style="margin-top:5px; font-size:10px; color:#94a3b8;">Facture générée le {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>