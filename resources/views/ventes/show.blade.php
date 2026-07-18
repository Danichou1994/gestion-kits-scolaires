@extends('layouts.app')

@section('title', 'Facture - ' . ($vente->client->prenom ?? '') . ' ' . ($vente->client->nom ?? ''))

@section('content')
<style>
    /* ========================================== */
    /* GESTION DES SAUTS DE PAGE                  */
    /* ========================================== */
    
    /* 🔥 Sections qui ne doivent PAS être coupées */
    .section-no-break {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    /* 🔥 Tableaux qui ne doivent PAS être coupés */
    .table-no-break {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    /* 🔥 Détail de la commande - PEUT être coupé */
    .section-break-ok {
        page-break-inside: auto !important;
        break-inside: auto !important;
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
    
    /* Masquer les actions à l'impression */
    .no-print {
        display: inline-block;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            font-size: 11px !important;
        }
        
        .section-no-break {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        
        .table-no-break {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        
        .section-break-ok {
            page-break-inside: auto !important;
            break-inside: auto !important;
        }
        
        .bg-white {
            background: white !important;
        }
        
        .shadow-lg {
            box-shadow: none !important;
        }
        
        .rounded-lg {
            border-radius: 0 !important;
        }
        
        .border {
            border: 1px solid #ddd !important;
        }
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
        
        <!-- ========================================== -->
        <!-- EN-TÊTE                                     -->
        <!-- ========================================== -->
        <div class="text-center border-b-2 border-blue-600 pb-4 mb-6 section-no-break">
            <h1 class="text-2xl font-bold text-blue-600">LA GRÂCE LUMINEUSE</h1>
            <p class="text-gray-600">Lomé - Togo</p>
            <p>📞 92108545 | 📧 gracelumineuse01@gmail.com</p>
        </div>

        <!-- ========================================== -->
        <!-- TITRE FACTURE AVEC NOM DU CLIENT           -->
        <!-- ========================================== -->
        <div class="text-center mb-6 section-no-break">
            <h2 class="text-xl font-bold text-gray-800">
                FACTURE <span class="text-blue-600">{{ $vente->numero_vente }}</span>
            </h2>
            <p class="text-gray-600">
                Client : <strong>{{ $vente->client->prenom ?? '' }} {{ $vente->client->nom ?? '' }}</strong>
            </p>
            <p class="text-gray-600">Date : {{ $vente->date_vente->format('d/m/Y') }}</p>
            <p>
                Statut : 
                @if($vente->statut == 'en_cours')
                    <span class="text-yellow-600 font-bold">⏳ En cours</span>
                @elseif($vente->statut == 'termine')
                    <span class="text-green-600 font-bold">✅ Terminé</span>
                @else
                    <span class="text-red-600 font-bold">❌ Annulé</span>
                @endif
            </p>
            <p>
                Mode de paiement :
                @if($vente->mode_paiement == 'especes')
                    <span class="text-green-600 font-bold">💵 Comptant</span>
                @elseif($vente->mode_paiement == 'mobile_money')
                    <span class="text-blue-600 font-bold">📱 Mobile Money</span>
                @else
                    <span class="text-yellow-600 font-bold">🔄 Tontine</span>
                @endif
            </p>
        </div>

        <!-- ========================================== -->
        <!-- INFOS CLIENT                                -->
        <!-- ========================================== -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6 section-no-break">
            <h3 class="font-bold text-lg">👤 CLIENT</h3>
            <p><strong>{{ $vente->client->prenom ?? '' }} {{ $vente->client->nom ?? '' }}</strong></p>
            <p>📞 {{ $vente->client->telephone ?? '' }}</p>
            @if(isset($vente->client->quartier) && $vente->client->quartier)
                <p>📍 {{ $vente->client->quartier }}</p>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- 📦 DÉTAIL DE LA COMMANDE - PEUT SE COUPER  -->
        <!-- ========================================== -->
        <div class="section-break-ok">
            <h3 class="font-bold text-lg mb-3">📦 DÉTAIL DE LA COMMANDE</h3>
            <table class="w-full border-collapse mb-6">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="p-2 text-left">Produit</th>
                        <th class="p-2 text-center">Qté</th>
                        <th class="p-2 text-right">Prix unitaire</th>
                        <th class="p-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $items = json_decode($vente->items, true) ?? [];
                    @endphp
                    @forelse($items as $item)
                    <tr class="border-b">
                        <td class="p-2">{{ $item['nom'] ?? 'Produit' }}</td>
                        <td class="p-2 text-center">{{ $item['quantite'] ?? 1 }}</td>
                        <td class="p-2 text-right">{{ number_format($item['prix'] ?? 0) }} F</td>
                        <td class="p-2 text-right">{{ number_format($item['total'] ?? 0) }} F</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-2 text-center text-gray-500">Aucun article trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t">
                        <td colspan="3" class="p-2 text-right font-bold">Sous-total</td>
                        <td class="p-2 text-right">{{ number_format($vente->sous_total ?? 0) }} F</td>
                    </tr>
                    @if(($vente->remise ?? 0) > 0)
                    <tr>
                        <td colspan="3" class="p-2 text-right">Réduction</td>
                        <td class="p-2 text-right text-red-600">- {{ number_format($vente->remise) }} F</td>
                    </tr>
                    @endif
                    @if(($vente->commission ?? 0) > 0)
                    <tr>
                        <td colspan="3" class="p-2 text-right">Commission (5%)</td>
                        <td class="p-2 text-right text-blue-600">+ {{ number_format($vente->commission) }} F</td>
                    </tr>
                    @endif
                    @if(($vente->frais_livraison ?? 0) > 0)
                    <tr>
                        <td colspan="3" class="p-2 text-right">Frais livraison</td>
                        <td class="p-2 text-right">{{ number_format($vente->frais_livraison) }} F</td>
                    </tr>
                    @endif
                    @if(($vente->frais_carnet ?? 0) > 0)
                    <tr>
                        <td colspan="3" class="p-2 text-right">Frais carnet</td>
                        <td class="p-2 text-right">{{ number_format($vente->frais_carnet) }} F</td>
                    </tr>
                    @endif
                    <tr class="bg-blue-50 font-bold">
                        <td colspan="3" class="p-2 text-right text-lg">TOTAL À PAYER</td>
                        <td class="p-2 text-right text-lg text-blue-600">{{ number_format($vente->montant_total ?? 0) }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- ========================================== -->
        <!-- 💳 MODE DE PAIEMENT - NE PAS COUPER        -->
        <!-- ========================================== -->
        <div class="section-no-break">
            <h3 class="font-bold text-lg mb-3">💳 MODE DE PAIEMENT : 
                @if($vente->mode_paiement == 'especes')
                    💵 Comptant
                @elseif($vente->mode_paiement == 'mobile_money')
                    📱 Mobile Money
                @else
                    🔄 Tontine
                @endif
            </h3>
            <table class="w-full mb-4 border-collapse table-no-break">
                <tr class="border-b">
                    <td class="p-2"><strong>Acompte (25%)</strong></td>
                    <td class="p-2 text-right">{{ number_format($vente->acompte ?? 0) }} F</td>
                    <td class="p-2 text-green-600 font-bold">✅ PAYÉ</td>
                </tr>
                <tr class="border-b">
                    <td class="p-2"><strong>Solde restant</strong></td>
                    <td class="p-2 text-right">{{ number_format($vente->solde ?? 0) }} F</td>
                    <td></td>
                </tr>
                @if($vente->mode_paiement == 'tontine')
                <tr>
                    <td class="p-2"><strong>Mensualités ({{ $vente->nb_mensualites ?? 0 }} mois)</strong></td>
                    <td class="p-2 text-right">{{ number_format($vente->montant_mensualite ?? 0) }} F/mois</td>
                    <td></td>
                </tr>
                @endif
            </table>
        </div>

        <!-- ========================================== -->
        <!-- 📅 ÉCHÉANCES - NE PAS COUPER               -->
        <!-- ========================================== -->
        @if($vente->mode_paiement == 'tontine')
        <div class="section-no-break">
            <h3 class="font-bold text-lg mb-3">📅 ÉCHÉANCES</h3>
            <table class="w-full border-collapse mb-6 table-no-break">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2 text-left">N°</th>
                        <th class="p-2 text-left">Date</th>
                        <th class="p-2 text-right">Montant</th>
                        <th class="p-2 text-center">Statut</th>
                        <th class="p-2 text-center no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vente->echeances as $index => $echeance)
                    <tr class="border-b">
                        <td class="p-2">{{ $index + 1 }}</td>
                        <td class="p-2">{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</td>
                        <td class="p-2 text-right">{{ number_format($echeance->montant_dû ?? 0) }} F</td>
                        <td class="p-2 text-center">
                            @if($echeance->statut == 'payé')
                                <span class="text-green-600 font-bold">✅ Payé</span>
                            @elseif($echeance->statut == 'en_retard')
                                <span class="text-red-600 font-bold">⚠️ En retard</span>
                            @else
                                <span class="text-yellow-600">⏳ À venir</span>
                            @endif
                        </td>
                        <td class="p-2 text-center no-print">
                            @if($echeance->statut != 'payé')
                                <a href="{{ route('echeances.payer', $echeance->id) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm hover:bg-green-700">Payer</a>
                                <a href="{{ route('echeances.retard', $echeance->id) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm hover:bg-red-700">Retard</a>
                            @else
                                <span class="text-gray-400 text-sm">✅ Payé</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-2 text-center text-gray-500">Aucune échéance trouvée</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        <!-- ========================================== -->
        <!-- 💰 COMMISSION TONTINE - NE PAS COUPER      -->
        <!-- ========================================== -->
        <div class="bg-blue-50 border-2 border-blue-200 p-4 rounded-lg mb-6 section-no-break">
            <h4 class="font-bold text-lg text-blue-800 mb-2">💰 COMMISSION TONTINE</h4>
            <table class="w-full table-no-break">
                <tr class="border-b">
                    <td class="p-2"><strong>Montant de la commande</strong></td>
                    <td class="p-2 text-right">{{ number_format($vente->sous_total ?? 0) }} F</td>
                </tr>
                <tr class="border-b">
                    <td class="p-2"><strong>Mode de paiement</strong></td>
                    <td class="p-2 text-right">
                        @if($vente->mode_paiement == 'especes')
                            <span class="text-green-600 font-bold">💵 Comptant</span>
                        @elseif($vente->mode_paiement == 'mobile_money')
                            <span class="text-blue-600 font-bold">📱 Mobile Money</span>
                        @else
                            <span class="text-yellow-600 font-bold">🔄 Tontine</span>
                        @endif
                    </td>
                </tr>
                @if($vente->mode_paiement == 'tontine')
                <!-- 💰 COMMISSION TONTINE -->
<tr class="border-b bg-blue-100">
    <td class="p-2"><strong>💵 Commission tontine (10%)</strong></td>
    <td class="p-2 text-right text-blue-600 font-bold text-lg">
        {{ number_format($vente->commission ?? 0) }} F
    </td>
</tr>
                @else
                <tr class="border-b bg-green-50">
                    <td class="p-2"><strong>✅ Exonéré (paiement comptant)</strong></td>
                    <td class="p-2 text-right text-green-600 font-bold">0 F</td>
                </tr>
                @endif
                <tr>
                    <td class="p-2"><strong>🏦 Montant net à payer</strong></td>
                    <td class="p-2 text-right text-green-600 font-bold text-lg">
                        {{ number_format($vente->montant_net ?? 0) }} F
                    </td>
                </tr>
            </table>
        </div>

        <!-- ========================================== -->
        <!-- 📊 RÉCAPITULATIF COMPLET - NE PAS COUPER   -->
        <!-- ========================================== -->
        <div class="border-2 border-blue-300 bg-blue-50 p-4 rounded-lg mb-6 section-no-break">
            <h4 class="font-bold text-lg text-blue-800 mb-3">📊 RÉCAPITULATIF COMPLET</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Paiements -->
                <div class="bg-white p-3 rounded-lg section-no-break">
                    <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">💳 PAIEMENTS</h5>
                    <table class="w-full text-sm table-no-break">
                        <tr class="border-b">
                            <td class="py-1">Acompte (25%)</td>
                            <td class="py-1 text-right font-bold text-green-600">{{ number_format($vente->acompte ?? 0) }} F</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-1">Solde restant</td>
                            <td class="py-1 text-right font-bold text-orange-600">{{ number_format($vente->solde ?? 0) }} F</td>
                        </tr>
                        @if($vente->mode_paiement == 'tontine')
                        <tr class="border-b">
                            <td class="py-1">Nombre de mensualités</td>
                            <td class="py-1 text-right font-bold">{{ $vente->nb_mensualites ?? 0 }} mois</td>
                        </tr>
                        <tr>
                            <td class="py-1">Montant par mensualité</td>
                            <td class="py-1 text-right font-bold">{{ number_format($vente->montant_mensualite ?? 0) }} F</td>
                        </tr>
                        @endif
                    </table>
                </div>
                
                <!-- Commissions -->
                <div class="bg-white p-3 rounded-lg section-no-break">
                    <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">💰 COMMISSIONS</h5>
                    <table class="w-full text-sm table-no-break">
                        <tr class="border-b">
                            <td class="py-1">Mode de paiement</td>
                            <td class="py-1 text-right">
                                @if($vente->mode_paiement == 'especes')
                                    <span class="text-green-600 font-bold">💵 Comptant</span>
                                @elseif($vente->mode_paiement == 'mobile_money')
                                    <span class="text-blue-600 font-bold">📱 Mobile Money</span>
                                @else
                                    <span class="text-yellow-600 font-bold">🔄 Tontine</span>
                                @endif
                            </td>
                        </tr>
                        @if($vente->mode_paiement == 'tontine')
                        <tr class="border-b bg-blue-50">
                            <td class="py-1 font-bold text-blue-700">💵 Commission (10%)</td>
                            <td class="py-1 text-right font-bold text-blue-600">{{ number_format($vente->commission ?? 0) }} F</td>
                        </tr>
                        @else
                        <tr class="border-b bg-green-50">
                            <td class="py-1 font-bold text-green-700">✅ Exonéré</td>
                            <td class="py-1 text-right font-bold text-green-600">0 F</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="py-1 font-bold text-green-700">🏦 Net à payer</td>
                            <td class="py-1 text-right font-bold text-green-600">{{ number_format($vente->montant_net ?? 0) }} F</td>
                        </tr>
                    </table>
                </div>
                
                @if($vente->mode_paiement == 'tontine')
                <!-- Échéances -->
                <div class="bg-white p-3 rounded-lg section-no-break">
                    <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">📅 ÉCHÉANCES</h5>
                    <table class="w-full text-sm table-no-break">
                        <tr class="border-b">
                            <td class="py-1">Total des échéances</td>
                            <td class="py-1 text-right">{{ $vente->echeances->count() }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-1 text-green-600">✅ Payées</td>
                            <td class="py-1 text-right text-green-600 font-bold">
                                {{ $vente->echeances->where('statut', 'payé')->count() }}
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-1 text-yellow-600">⏳ En attente</td>
                            <td class="py-1 text-right text-yellow-600 font-bold">
                                {{ $vente->echeances->where('statut', 'en_attente')->count() }}
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 text-red-600">⚠️ En retard</td>
                            <td class="py-1 text-right text-red-600 font-bold">
                                {{ $vente->echeances->where('statut', 'en_retard')->count() }}
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Totaux -->
                <div class="bg-white p-3 rounded-lg section-no-break">
                    <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">📊 TOTAUX</h5>
                    @php
                        $totalPaye = $vente->echeances()->where('statut', 'payé')->sum('montant_dû') ?? 0;
                        $totalRestant = ($vente->solde ?? 0) - $totalPaye;
                        $prochaine = $vente->echeances()->where('statut', 'en_attente')->orderBy('date_echeance')->first();
                    @endphp
                    <table class="w-full text-sm table-no-break">
                        <tr class="border-b">
                            <td class="py-1">Total payé</td>
                            <td class="py-1 text-right font-bold text-green-600">{{ number_format($totalPaye) }} F</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-1">Reste à payer</td>
                            <td class="py-1 text-right font-bold text-red-600">{{ number_format($totalRestant) }} F</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-1">Prochaine échéance</td>
                            <td class="py-1 text-right font-bold">
                                @if($prochaine)
                                    {{ \Carbon\Carbon::parse($prochaine->date_echeance)->format('d/m/Y') }}
                                @else
                                    <span class="text-green-600">✅ Aucune</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 font-bold text-blue-700">Total commission</td>
                            <td class="py-1 text-right font-bold text-blue-600">{{ number_format($vente->commission ?? 0) }} F</td>
                        </tr>
                    </table>
                </div>
                @endif
                
            </div>
        </div>

        <!-- ========================================== -->
        <!-- PIED DE PAGE                                -->
        <!-- ========================================== -->
        <div class="text-center text-gray-600 text-sm mt-6 pt-4 border-t section-no-break">
            <p>📱 Contact : 92108545 | 📧 gracelumineuse01@gmail.com</p>
            <p class="font-bold">Merci de votre confiance !</p>
            <p class="text-xs">Ce document fait foi de contrat de vente à crédit.</p>
        </div>

        <!-- ========================================== -->
        <!-- ACTIONS                                     -->
        <!-- ========================================== -->
        <div class="flex flex-wrap justify-end gap-2 mt-6 no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                🖨️ Imprimer
            </button>
            <a href="{{ route('ventes.facture', $vente) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                📄 Télécharger PDF
            </a>
            <a href="{{ route('ventes.edit', $vente) }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                ✏️ Modifier
            </a>
            <a href="{{ route('ventes.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Retour
            </a>
        </div>

    </div>
</div>
@endsection