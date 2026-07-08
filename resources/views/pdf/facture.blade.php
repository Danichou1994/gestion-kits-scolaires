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
            background: #f8fafc;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 10px;
        }
        .header {
            border-bottom: 3px solid #2563EB;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo h1 {
            color: #2563EB;
            font-size: 24px;
        }
        .logo .etablissement {
            color: #1e293b;
            font-weight: bold;
            font-size: 18px;
        }
        .logo .tel {
            color: #64748b;
            font-size: 14px;
        }
        .facture-title h2 {
            color: #2563EB;
            font-size: 28px;
        }
        .facture-title p {
            color: #64748b;
            font-size: 14px;
        }
        .facture-title .num {
            color: #2563EB;
            font-weight: bold;
            font-size: 16px;
        }
        .client-info {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .client-info h3 {
            color: #1e293b;
            margin-bottom: 5px;
        }
        .client-info p {
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
            font-size: 13px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .recap {
            margin-top: 20px;
            padding: 15px;
            background: #f1f5f9;
            border-radius: 8px;
            max-width: 400px;
            margin-left: auto;
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
            font-size: 18px;
            color: #2563EB;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 12px;
        }
        .statut-paye { color: #16a34a; font-weight: bold; }
        .statut-attente { color: #d97706; font-weight: bold; }
        .statut-retard { color: #dc2626; font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-en-cours { background: #fef3c7; color: #92400e; }
        .badge-termine { background: #d1fae5; color: #065f46; }
        .badge-annule { background: #fee2e2; color: #991b1b; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        .text-gray { color: #64748b; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- En-tête -->
        <div class="header">
            <div class="header-top">
                <div class="logo">
                    <h1>📚 La Lumiere_Divine</h1>
                    <div class="etablissement">🏫 École La Lumiere_Divine</div>
                    <div class="tel">📞 +228 92 10 85 45</div>
                    <div class="tel">📍 Lomé - Togo</div>
                </div>
                <div class="facture-title">
                    <h2>FACTURE</h2>
                    <p class="num">N° {{ $vente->numero_vente }}</p>
                    <p>Date: {{ $vente->date_vente->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Client -->
        <div class="client-info">
            <h3>👤 Client</h3>
            <p><strong>{{ $vente->client->prenom }} {{ $vente->client->nom }}</strong></p>
            <p>📞 {{ $vente->client->telephone }}</p>
            @if($vente->client->email)
                <p>📧 {{ $vente->client->email }}</p>
            @endif
            @if($vente->client->adresse)
                <p>📍 {{ $vente->client->adresse }}{{ $vente->client->quartier ? ' - '.$vente->client->quartier : '' }}</p>
            @endif
        </div>

        <!-- Détails de la vente -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produit</th>
                    <th>Type</th>
                    <th>Qté</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($vente->details as $detail)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $detail->nom_produit }}</td>
                    <td>{{ $detail->type == 'article' ? '📦 Article' : '🎒 Kit' }}</td>
                    <td class="text-center">{{ $detail->quantite }}</td>
                    <td class="text-right">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F</td>
                    <td class="text-right">{{ number_format($detail->total_ligne, 0, ',', ' ') }} F</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Récapitulatif -->
        <div class="recap">
            <div class="recap-item">
                <span>📊 Montant HT</span>
                <span>{{ number_format($vente->montant_ht, 0, ',', ' ') }} F</span>
            </div>
            <div class="recap-item">
                <span>📊 TVA (18%)</span>
                <span>{{ number_format($vente->tva, 0, ',', ' ') }} F</span>
            </div>
            @if($vente->remise > 0)
            <div class="recap-item" style="color: #16a34a;">
                <span>🎯 Remise</span>
                <span>-{{ number_format($vente->remise, 0, ',', ' ') }} F</span>
            </div>
            @endif
            @if($vente->frais_livraison > 0)
            <div class="recap-item">
                <span>🚚 Frais livraison</span>
                <span>{{ number_format($vente->frais_livraison, 0, ',', ' ') }} F</span>
            </div>
            @endif
            @if($vente->frais_carnet > 0)
            <div class="recap-item">
                <span>📕 Frais carnet</span>
                <span>{{ number_format($vente->frais_carnet, 0, ',', ' ') }} F</span>
            </div>
            @endif
            <div class="recap-item total">
                <span>💰 NET À PAYER</span>
                <span>{{ number_format($vente->net_a_payer, 0, ',', ' ') }} F</span>
            </div>
        </div>

        <!-- Paiement -->
        <div style="margin-top: 20px; padding: 15px; background: #eff6ff; border-radius: 8px;">
            <h4 style="color: #1e40af; margin-bottom: 5px;">💳 Mode de paiement</h4>
            <p><strong>{{ ucfirst($vente->mode_paiement ?? 'Non spécifié') }}</strong></p>
            @if($vente->statut == 'en_cours')
                <p style="margin-top: 10px;">
                    <strong>✅ Acompte:</strong> {{ number_format($vente->acompte, 0, ',', ' ') }} F
                    <br>
                    <strong>📆 Solde:</strong> {{ number_format($vente->solde, 0, ',', ' ') }} F
                    <br>
                    <strong>📊 Mensualités:</strong> {{ $vente->nb_mensualites }} x {{ number_format($vente->montant_mensualite, 0, ',', ' ') }} F
                </p>
            @endif
            <p style="margin-top: 5px;">
                <strong>Statut:</strong>
                <span class="badge badge-{{ $vente->statut }}">
                    {{ $vente->statut == 'en_cours' ? '⏳ En cours' : ($vente->statut == 'termine' ? '✅ Terminé' : '❌ Annulé') }}
                </span>
            </p>
        </div>

        <!-- Échéances -->
        @if($vente->echeances->count() > 0)
        <div style="margin-top: 20px; padding: 15px; border: 1px solid #dbeafe; border-radius: 8px;">
            <h4 style="color: #1e40af; margin-bottom: 5px;">📅 Échéances</h4>
            <table style="margin: 10px 0;">
                <thead>
                    <tr>
                        <th style="background: #dbeafe; color: #1e40af;">Date</th>
                        <th style="background: #dbeafe; color: #1e40af;">Montant</th>
                        <th style="background: #dbeafe; color: #1e40af;">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vente->echeances as $echeance)
                    <tr>
                        <td>{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                        <td class="text-center">
                            @if($echeance->statut == 'paye')
                                <span class="statut-paye">✅ Payé</span>
                            @elseif($echeance->statut == 'en_attente')
                                <span class="statut-attente">⏳ En attente</span>
                            @else
                                <span class="statut-retard">⚠️ En retard</span>
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
            <p>🏫 La Lumiere_Divine - Lomé 📞 92 10 85 45</p>
            <p>📧 contact@lalumiere-divine.com</p>
            <p style="margin-top: 5px;">Merci pour votre confiance !</p>
        </div>
    </div>
</body>
</html>