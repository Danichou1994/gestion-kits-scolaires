<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $vente->numero_vente }}</title>
    <style>
        /* ========================================== */
        /* STYLES DE BASE                             */
        /* ========================================== */
        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            font-size: 11px; 
            padding: 20px;
            margin: 0;
        }
        
        /* ========================================== */
        /* GESTION DES SAUTS DE PAGE                   */
        /* ========================================== */
        .page-section {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            page-break-after: avoid !important;
            break-after: avoid !important;
        }
        
        .page-table {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        
        /* Pour les tableaux longs */
        table {
            page-break-inside: auto !important;
            break-inside: auto !important;
        }
        
        tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        
        thead {
            display: table-header-group !important;
        }
        
        tfoot {
            display: table-footer-group !important;
        }
        
        /* ========================================== */
        /* STYLES DE MISE EN PAGE                     */
        /* ========================================== */
        .header { 
            text-align: center; 
            border-bottom: 2px solid #2563EB; 
            padding-bottom: 10px; 
        }
        
        .title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #2563EB; 
        }
        
        .subtitle { 
            color: #666; 
            font-size: 12px; 
        }
        
        .client-info { 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        
        .section-title { 
            font-weight: bold; 
            font-size: 13px; 
            margin: 10px 0 5px 0; 
            color: #1E40AF; 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 5px 0; 
        }
        
        th { 
            background: #2563EB; 
            color: white; 
            padding: 6px; 
            text-align: left; 
            font-size: 10px; 
        }
        
        td { 
            padding: 5px; 
            border-bottom: 1px solid #ddd; 
        }
        
        .text-right { 
            text-align: right; 
        }
        
        .text-center { 
            text-align: center; 
        }
        
        .font-bold { 
            font-weight: bold; 
        }
        
        .total-row { 
            background: #DBEAFE; 
            font-weight: bold; 
        }
        
        .commission-box { 
            background: #EFF6FF; 
            border: 2px solid #93C5FD; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        
        .recap-box { 
            background: #F0FDF4; 
            border: 2px solid #86EFAC; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        
        .exonere-box { 
            background: #F0FDF4; 
            border: 2px solid #86EFAC; 
            padding: 10px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        
        .footer { 
            text-align: center; 
            margin-top: 20px; 
            color: #666; 
            font-size: 9px; 
            border-top: 1px solid #ddd; 
            padding-top: 10px; 
        }
        
        .text-blue { 
            color: #2563EB; 
        }
        
        .text-green { 
            color: #16A34A; 
        }
        
        .text-red { 
            color: #DC2626; 
        }
        
        .text-yellow { 
            color: #F59E0B; 
        }
        
        .grid-2 { 
            display: table; 
            width: 100%; 
        }
        
        .grid-2 > div { 
            display: table-cell; 
            width: 50%; 
            padding: 5px; 
            vertical-align: top; 
        }
        
        .box { 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            padding: 8px; 
            margin: 3px; 
        }
        
        .bg-blue-50 { 
            background: #EFF6FF; 
        }
        
        .bg-green-50 { 
            background: #F0FDF4; 
        }
    </style>
</head>
<body>

    <!-- ========================================== -->
    <!-- EN-TÊTE                                     -->
    <!-- ========================================== -->
    <div class="header page-section">
        <h1 class="title">LA GRÂCE LUMINEUSE</h1>
        <p class="subtitle">Lomé - Togo</p>
        <p>📞 92108545 | 📧 gracelumineuse01@gmail.com</p>
    </div>

    <!-- ========================================== -->
    <!-- TITRE FACTURE                                -->
    <!-- ========================================== -->
    <div style="text-align: center; margin: 10px 0;" class="page-section">
        <h2 style="font-size: 16px;">
            FACTURE <span style="color: #2563EB;">{{ $vente->numero_vente }}</span>
        </h2>
        <p style="margin: 2px 0;">
            Client : <strong>{{ $vente->client->prenom ?? '' }} {{ $vente->client->nom ?? '' }}</strong>
        </p>
        <p style="margin: 2px 0;">Date : {{ $vente->date_vente->format('d/m/Y') }}</p>
        <p style="margin: 2px 0;">
            Statut : 
            @if($vente->statut == 'en_cours')
                <span style="color: #F59E0B; font-weight: bold;">⏳ En cours</span>
            @elseif($vente->statut == 'termine')
                <span style="color: #16A34A; font-weight: bold;">✅ Terminé</span>
            @else
                <span style="color: #DC2626; font-weight: bold;">❌ Annulé</span>
            @endif
        </p>
        <p style="margin: 2px 0;">
            Mode de paiement :
            @if($vente->mode_paiement == 'especes')
                <span style="color: #16A34A; font-weight: bold;">💵 Comptant</span>
            @elseif($vente->mode_paiement == 'mobile_money')
                <span style="color: #2563EB; font-weight: bold;">📱 Mobile Money</span>
            @else
                <span style="color: #F59E0B; font-weight: bold;">🔄 Tontine</span>
            @endif
        </p>
    </div>

    <!-- ========================================== -->
    <!-- INFOS CLIENT                                -->
    <!-- ========================================== -->
    <div class="client-info page-section">
        <p style="margin: 2px 0;"><strong>👤 CLIENT</strong></p>
        <p style="margin: 2px 0;"><strong>{{ $vente->client->prenom ?? '' }} {{ $vente->client->nom ?? '' }}</strong></p>
        <p style="margin: 2px 0;">📞 {{ $vente->client->telephone ?? '' }}</p>
        @if(isset($vente->client->quartier) && $vente->client->quartier)
            <p style="margin: 2px 0;">📍 {{ $vente->client->quartier }}</p>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- DÉTAIL DE LA COMMANDE                       -->
    <!-- ========================================== -->
    <div class="page-section">
        <p class="section-title">📦 DÉTAIL DE LA COMMANDE</p>
        <table class="page-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th class="text-center">Qté</th>
                    <th class="text-right">Prix unit.</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = json_decode($vente->items, true) ?? [];
                @endphp
                @forelse($items as $item)
                <tr>
                    <td>{{ $item['nom'] ?? 'Produit' }}</td>
                    <td class="text-center">{{ $item['quantite'] ?? 1 }}</td>
                    <td class="text-right">{{ number_format($item['prix'] ?? 0) }} F</td>
                    <td class="text-right">{{ number_format($item['total'] ?? 0) }} F</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Aucun article</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right font-bold">Sous-total</td>
                    <td class="text-right">{{ number_format($vente->sous_total ?? 0) }} F</td>
                </tr>
                @if(($vente->remise ?? 0) > 0)
                <tr>
                    <td colspan="3" class="text-right">Réduction</td>
                    <td class="text-right" style="color: #DC2626;">- {{ number_format($vente->remise) }} F</td>
                </tr>
                @endif
                @if(($vente->commission ?? 0) > 0)
                <tr>
                    <td colspan="3" class="text-right">Commission (5%)</td>
                    <td class="text-right" style="color: #2563EB;">+ {{ number_format($vente->commission) }} F</td>
                </tr>
                @endif
                @if(($vente->frais_livraison ?? 0) > 0)
                <tr>
                    <td colspan="3" class="text-right">Frais livraison</td>
                    <td class="text-right">{{ number_format($vente->frais_livraison) }} F</td>
                </tr>
                @endif
                @if(($vente->frais_carnet ?? 0) > 0)
                <tr>
                    <td colspan="3" class="text-right">Frais carnet</td>
                    <td class="text-right">{{ number_format($vente->frais_carnet) }} F</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL À PAYER</td>
                    <td class="text-right" style="color: #2563EB; font-size: 14px;">
                        {{ number_format($vente->montant_total ?? 0) }} F
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- ========================================== -->
    <!-- MODE DE PAIEMENT                            -->
    <!-- ========================================== -->
    <div class="page-section">
        <p class="section-title">💳 MODE DE PAIEMENT : 
            @if($vente->mode_paiement == 'especes')
                💵 Comptant
            @elseif($vente->mode_paiement == 'mobile_money')
                📱 Mobile Money
            @else
                🔄 Tontine
            @endif
        </p>
        <table class="page-table">
            <tr>
                <td><strong>Acompte (25%)</strong></td>
                <td class="text-right">{{ number_format($vente->acompte ?? 0) }} F</td>
                <td style="color: #16A34A; font-weight: bold;">✅ PAYÉ</td>
            </tr>
            <tr>
                <td><strong>Solde restant</strong></td>
                <td class="text-right">{{ number_format($vente->solde ?? 0) }} F</td>
                <td></td>
            </tr>
            @if($vente->mode_paiement == 'tontine')
            <tr>
                <td><strong>Mensualités ({{ $vente->nb_mensualites ?? 0 }} mois)</strong></td>
                <td class="text-right">{{ number_format($vente->montant_mensualite ?? 0) }} F/mois</td>
                <td></td>
            </tr>
            @endif
        </table>
    </div>

    @if($vente->mode_paiement == 'tontine')
    <!-- ========================================== -->
    <!-- ÉCHÉANCES                                   -->
    <!-- ========================================== -->
    <div class="page-section">
        <p class="section-title">📅 ÉCHÉANCES</p>
        <table class="page-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th class="text-right">Montant</th>
                    <th class="text-center">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vente->echeances as $index => $echeance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($echeance->montant_dû ?? 0) }} F</td>
                    <td class="text-center">
                        @if($echeance->statut == 'payé')
                            <span style="color: #16A34A; font-weight: bold;">✅ Payé</span>
                        @elseif($echeance->statut == 'en_retard')
                            <span style="color: #DC2626; font-weight: bold;">⚠️ En retard</span>
                        @else
                            <span style="color: #F59E0B;">⏳ En attente</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Aucune échéance</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- 💰 COMMISSION TONTINE                       -->
    <!-- ========================================== -->
    @if($vente->mode_paiement == 'tontine')
    <div class="commission-box page-section">
        <p style="font-weight: bold; margin: 0 0 5px 0; color: #1E40AF; font-size: 13px;">💰 COMMISSION TONTINE</p>
        <table class="page-table">
            <tr>
                <td><strong>Montant de la commande</strong></td>
                <td class="text-right">{{ number_format($vente->sous_total ?? 0) }} F</td>
            </tr>
            <tr style="background: #DBEAFE;">
                <td><strong>💵 Commission (5%)</strong></td>
                <td class="text-right" style="color: #2563EB; font-weight: bold; font-size: 13px;">
                    {{ number_format($vente->commission ?? 0) }} F
                </td>
            </tr>
            <tr>
                <td><strong>🏦 Montant net à payer</strong></td>
                <td class="text-right" style="color: #16A34A; font-weight: bold;">
                    {{ number_format($vente->montant_net ?? 0) }} F
                </td>
            </tr>
        </table>
    </div>
    @else
    <div class="exonere-box page-section">
        <p style="font-weight: bold; margin: 0 0 5px 0; color: #166534; font-size: 13px;">✅ EXONÉRÉ DE COMMISSION</p>
        <table class="page-table">
            <tr>
                <td><strong>Mode de paiement</strong></td>
                <td class="text-right" style="color: #16A34A; font-weight: bold;">
                    @if($vente->mode_paiement == 'especes') 💵 Comptant @else 📱 Mobile Money @endif
                </td>
            </tr>
            <tr>
                <td><strong>Commission appliquée</strong></td>
                <td class="text-right" style="color: #16A34A; font-weight: bold;">0 F</td>
            </tr>
            <tr>
                <td><strong>🏦 Montant net à payer</strong></td>
                <td class="text-right" style="color: #16A34A; font-weight: bold;">
                    {{ number_format($vente->montant_net ?? 0) }} F
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- ========================================== -->
    <!-- 📊 RÉCAPITULATIF COMPLET                    -->
    <!-- ========================================== -->
    <div class="recap-box page-section">
        <p style="font-weight: bold; margin: 0 0 5px 0; color: #166534; font-size: 13px;">📊 RÉCAPITULATIF COMPLET</p>
        
        <div class="grid-2">
            <div>
                <div class="box">
                    <p style="font-weight: bold; margin: 0 0 3px 0;">💳 PAIEMENTS</p>
                    <table style="font-size: 10px;" class="page-table">
                        <tr><td>Acompte (25%)</td><td class="text-right">{{ number_format($vente->acompte ?? 0) }} F</td></tr>
                        <tr><td>Solde restant</td><td class="text-right">{{ number_format($vente->solde ?? 0) }} F</td></tr>
                        @if($vente->mode_paiement == 'tontine')
                        <tr><td>Mensualités</td><td class="text-right">{{ $vente->nb_mensualites ?? 0 }} mois</td></tr>
                        <tr><td>Montant/mois</td><td class="text-right">{{ number_format($vente->montant_mensualite ?? 0) }} F</td></tr>
                        @endif
                    </table>
                </div>
            </div>
            <div>
                <div class="box">
                    <p style="font-weight: bold; margin: 0 0 3px 0;">💰 COMMISSIONS</p>
                    <table style="font-size: 10px;" class="page-table">
                        <tr><td>Mode paiement</td>
                            <td class="text-right">
                                @if($vente->mode_paiement == 'especes')
                                    <span style="color: #16A34A;">💵 Comptant</span>
                                @elseif($vente->mode_paiement == 'mobile_money')
                                    <span style="color: #2563EB;">📱 Mobile Money</span>
                                @else
                                    <span style="color: #F59E0B;">🔄 Tontine</span>
                                @endif
                            </td>
                        </tr>
                        @if($vente->mode_paiement == 'tontine')
                        <tr style="background: #DBEAFE;">
                            <td>💵 Commission (5%)</td>
                            <td class="text-right" style="color: #2563EB;">{{ number_format($vente->commission ?? 0) }} F</td>
                        </tr>
                        @else
                        <tr style="background: #F0FDF4;">
                            <td>✅ Exonéré</td>
                            <td class="text-right" style="color: #16A34A;">0 F</td>
                        </tr>
                        @endif
                        <tr><td>🏦 Net</td><td class="text-right" style="color: #16A34A;">{{ number_format($vente->montant_net ?? 0) }} F</td></tr>
                    </table>
                </div>
            </div>
        </div>
        
        @if($vente->mode_paiement == 'tontine')
        <div class="grid-2">
            <div>
                <div class="box">
                    <p style="font-weight: bold; margin: 0 0 3px 0;">📅 ÉCHÉANCES</p>
                    <table style="font-size: 10px;" class="page-table">
                        <tr><td>Total</td><td class="text-right">{{ $vente->echeances->count() }}</td></tr>
                        <tr><td style="color: #16A34A;">✅ Payées</td><td class="text-right" style="color: #16A34A;">{{ $vente->echeances->where('statut', 'payé')->count() }}</td></tr>
                        <tr><td style="color: #F59E0B;">⏳ En attente</td><td class="text-right" style="color: #F59E0B;">{{ $vente->echeances->where('statut', 'en_attente')->count() }}</td></tr>
                        <tr><td style="color: #DC2626;">⚠️ En retard</td><td class="text-right" style="color: #DC2626;">{{ $vente->echeances->where('statut', 'en_retard')->count() }}</td></tr>
                    </table>
                </div>
            </div>
            <div>
                <div class="box">
                    <p style="font-weight: bold; margin: 0 0 3px 0;">📊 TOTAUX</p>
                    @php
                        $totalPaye = $vente->echeances()->where('statut', 'payé')->sum('montant_dû') ?? 0;
                        $totalRestant = ($vente->solde ?? 0) - $totalPaye;
                        $prochaine = $vente->echeances()->where('statut', 'en_attente')->orderBy('date_echeance')->first();
                    @endphp
                    <table style="font-size: 10px;" class="page-table">
                        <tr><td>Total payé</td><td class="text-right" style="color: #16A34A;">{{ number_format($totalPaye) }} F</td></tr>
                        <tr><td>Reste à payer</td><td class="text-right" style="color: #DC2626;">{{ number_format($totalRestant) }} F</td></tr>
                        <tr><td>Prochaine échéance</td><td class="text-right">{{ $prochaine ? \Carbon\Carbon::parse($prochaine->date_echeance)->format('d/m/Y') : '✅ Aucune' }}</td></tr>
                        <tr><td style="color: #2563EB;">Total commission</td><td class="text-right" style="color: #2563EB;">{{ number_format($vente->commission ?? 0) }} F</td></tr>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- PIED DE PAGE                                -->
    <!-- ========================================== -->
    <div class="footer page-section">
        <p style="margin: 2px 0;">📱 Contact : 92108545 | 📧 gracelumineuse01@gmail.com</p>
        <p style="margin: 2px 0; font-weight: bold;">Merci de votre confiance !</p>
        <p style="margin: 2px 0; font-size: 8px;">Ce document fait foi de contrat de vente à crédit.</p>
        <p style="margin: 2px 0; font-size: 8px; color: #999;">Facture générée le {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>