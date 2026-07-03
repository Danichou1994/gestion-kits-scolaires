<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture #{{ $vente->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            padding: 30px;
            background: #f8fafc;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
            font-size: 28px;
        }
        .header-left p {
            color: #64748b;
            font-size: 14px;
        }
        .header-right {
            text-align: right;
        }
        .header-right h2 {
            color: #1e293b;
            font-size: 24px;
        }
        .header-right .facture-num {
            color: #2563EB;
            font-weight: bold;
        }
        .info-client {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-client h3 {
            color: #1e293b;
            margin-bottom: 5px;
        }
        .info-client p {
            color: #475569;
            font-size: 14px;
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: #2563EB;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .total-row {
            background: #f1f5f9;
            font-weight: bold;
        }
        .total-row td {
            border-bottom: none;
        }
        .montant {
            text-align: right;
            font-weight: bold;
        }
        .recap {
            margin-top: 20px;
            padding: 15px;
            background: #f1f5f9;
            border-radius: 8px;
        }
        .recap-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
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
            font-size: 12px;
        }
        .paiement-info {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #dbeafe;
            background: #eff6ff;
            border-radius: 8px;
        }
        .paiement-info h4 {
            color: #1e40af;
            margin-bottom: 5px;
        }
        .statut-paye { color: #16a34a; font-weight: bold; }
        .statut-attente { color: #d97706; font-weight: bold; }
        .statut-retard { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- En-tête -->
        <div class="header">
            <div class="header-left">
                <h1>📚 Gestion Kits</h1>
                <p>Lomé - Togo</p>
                <p>Tel: +228 XX XX XX XX</p>
            </div>
            <div class="header-right">
                <h2>FACTURE</h2>
                <p class="facture-num">N° {{ $vente->id }}</p>
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

        <!-- Détails du kit -->
        <h3>📦 Détails de l'achat</h3>
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php $totalArticles = 0; @endphp
                @foreach($vente->kit->articles as $article)
                <tr>
                    <td>{{ $article->nom_article }}</td>
                    <td>{{ $article->pivot->quantite }}</td>
                    <td>{{ number_format($article->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td class="montant">{{ number_format($article->prix_unitaire * $article->pivot->quantite, 0, ',', ' ') }} F</td>
                </tr>
                @php $totalArticles += $article->prix_unitaire * $article->pivot->quantite; @endphp
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Total articles</td>
                    <td class="montant">{{ number_format($totalArticles, 0, ',', ' ') }} F</td>
                </tr>
            </tbody>
        </table>

        <!-- Récapitulatif paiement -->
        <div class="recap">
            <div class="recap-item">
                <span>💰 Montant total</span>
                <span><strong>{{ number_format($vente->montant_total, 0, ',', ' ') }} F</strong></span>
            </div>
            <div class="recap-item">
                <span>💳 Acompte (1/4)</span>
                <span><strong>{{ number_format($vente->acompte, 0, ',', ' ') }} F</strong></span>
            </div>
            <div class="recap-item">
                <span>📆 Solde restant</span>
                <span><strong>{{ number_format($vente->solde, 0, ',', ' ') }} F</strong></span>
            </div>
            <div class="recap-item">
                <span>📊 Mensualités</span>
                <span>{{ $vente->nb_mensualites }} x {{ number_format($vente->montant_mensualite, 0, ',', ' ') }} F</span>
            </div>
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
        <div class="paiement-info">
            <h4>📅 Échéances</h4>
            <table style="margin: 10px 0;">
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
                        <td>{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
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
            <p>Gestion Kits Scolaires - Lomé</p>
        </div>
    </div>
</body>
</html>