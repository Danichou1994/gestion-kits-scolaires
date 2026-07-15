@extends('layouts.app')

@section('title', 'Détail de la vente')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
        
        <!-- ========================================== -->
        <!-- EN-TÊTE                                     -->
        <!-- ========================================== -->
        <div class="text-center border-b-2 border-blue-600 pb-4 mb-6">
            <h1 class="text-2xl font-bold text-blue-600">LA GRÂCE LUMINEUSE</h1>
            <p class="text-gray-600">Lomé - Togo</p>
            <p>📞 92108545 | 📧 gracelumineuse01@gmail.com</p>
        </div>

        <!-- ========================================== -->
        <!-- INFOS FACTURE                               -->
        <!-- ========================================== -->
        <div class="flex flex-wrap justify-between mb-4">
            <div>
                <strong>FACTURE N°</strong> {{ $vente->numero_vente }}
            </div>
            <div>
                <strong>Date</strong> {{ $vente->date_vente->format('d/m/Y') }}
            </div>
            <div>
                <strong>Statut</strong> 
                @if($vente->statut == 'en_cours')
                    <span class="text-yellow-600 font-bold">⏳ En cours</span>
                @elseif($vente->statut == 'termine')
                    <span class="text-green-600 font-bold">✅ Terminé</span>
                @else
                    <span class="text-red-600 font-bold">❌ Annulé</span>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INFOS CLIENT                                -->
        <!-- ========================================== -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="font-bold text-lg">👤 CLIENT</h3>
            <p><strong>{{ $vente->client->prenom }} {{ $vente->client->nom }}</strong></p>
            <p>📞 {{ $vente->client->telephone }}</p>
            @if($vente->client->quartier)
                <p>📍 {{ $vente->client->quartier }}</p>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- DÉTAIL DE LA COMMANDE                       -->
        <!-- ========================================== -->
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
                @foreach($items as $item)
                <tr class="border-b">
                    <td class="p-2">{{ $item['nom'] ?? 'Produit' }}</td>
                    <td class="p-2 text-center">{{ $item['quantite'] ?? 1 }}</td>
                    <td class="p-2 text-right">{{ number_format($item['prix'] ?? 0) }} F</td>
                    <td class="p-2 text-right">{{ number_format($item['total'] ?? 0) }} F</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t">
                    <td colspan="3" class="p-2 text-right font-bold">Sous-total</td>
                    <td class="p-2 text-right">{{ number_format($vente->sous_total) }} F</td>
                </tr>
                @if($vente->remise > 0)
                <tr>
                    <td colspan="3" class="p-2 text-right">Réduction</td>
                    <td class="p-2 text-right text-red-600">- {{ number_format($vente->remise) }} F</td>
                </tr>
                @endif
                @if($vente->frais_livraison > 0)
                <tr>
                    <td colspan="3" class="p-2 text-right">Frais livraison</td>
                    <td class="p-2 text-right">{{ number_format($vente->frais_livraison) }} F</td>
                </tr>
                @endif
                @if($vente->frais_carnet > 0)
                <tr>
                    <td colspan="3" class="p-2 text-right">Frais carnet</td>
                    <td class="p-2 text-right">{{ number_format($vente->frais_carnet) }} F</td>
                </tr>
                @endif
                <tr class="bg-blue-50 font-bold">
                    <td colspan="3" class="p-2 text-right text-lg">TOTAL À PAYER</td>
                    <td class="p-2 text-right text-lg text-blue-600">{{ number_format($vente->montant_total) }} F</td>
                </tr>
            </tfoot>
        </table>

        <!-- ========================================== -->
        <!-- MODE DE PAIEMENT : TONTINE                  -->
        <!-- ========================================== -->
        <h3 class="font-bold text-lg mb-3">💳 MODE DE PAIEMENT : TONTINE</h3>
        <table class="w-full mb-4 border-collapse">
            <tr class="border-b">
                <td class="p-2"><strong>Acompte (25%)</strong></td>
                <td class="p-2 text-right">{{ number_format($vente->acompte) }} F</td>
                <td class="p-2 text-green-600 font-bold">✅ PAYÉ</td>
            </tr>
            <tr class="border-b">
                <td class="p-2"><strong>Solde restant</strong></td>
                <td class="p-2 text-right">{{ number_format($vente->solde) }} F</td>
                <td></td>
            </tr>
            <tr>
                <td class="p-2"><strong>Mensualités ({{ $vente->nb_mensualites }} mois)</strong></td>
                <td class="p-2 text-right">{{ number_format($vente->montant_mensualite) }} F/mois</td>
                <td></td>
            </tr>
        </table>

        <!-- ========================================== -->
        <!-- ÉCHÉANCES                                   -->
        <!-- ========================================== -->
        <h3 class="font-bold text-lg mb-3">📅 ÉCHÉANCES</h3>
        <table class="w-full border-collapse mb-6">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">N°</th>
                    <th class="p-2 text-left">Date</th>
                    <th class="p-2 text-right">Montant</th>
                    <th class="p-2 text-center">Statut</th>
                    <th class="p-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vente->echeances as $index => $echeance)
                <tr class="border-b">
                    <td class="p-2">{{ $index + 1 }}</td>
                    <td class="p-2">{{ \Carbon\Carbon::parse($echeance->date_echeance)->format('d/m/Y') }}</td>
                    <td class="p-2 text-right">{{ number_format($echeance->montant_dû) }} F</td>
                    <td class="p-2 text-center">
                        @if($echeance->statut == 'payé')
                            <span class="text-green-600 font-bold">✅ Payé</span>
                        @elseif($echeance->statut == 'en_retard')
                            <span class="text-red-600 font-bold">⚠️ En retard</span>
                        @else
                            <span class="text-yellow-600">⏳ À venir</span>
                        @endif
                    </td>
                    <td class="p-2 text-center">
                        @if($echeance->statut != 'payé')
                            <a href="{{ route('echeances.payer', $echeance->id) }}" class="bg-green-600 text-white px-2 py-1 rounded text-sm hover:bg-green-700">Payer</a>
                            <a href="{{ route('echeances.retard', $echeance->id) }}" class="bg-red-600 text-white px-2 py-1 rounded text-sm hover:bg-red-700">Retard</a>
                        @else
                            <span class="text-gray-400 text-sm">✅ Payé</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

      <!-- ========================================== -->
<!-- 💰 MA COMMISSION (10%)                      -->
<!-- ========================================== -->
<div class="bg-blue-50 border-2 border-blue-200 p-4 rounded-lg mb-6">
    <h4 class="font-bold text-lg text-blue-800 mb-2">💰 MA COMMISSION</h4>
    <table class="w-full">
        <tr class="border-b">
            <td class="p-2"><strong>Montant total de la vente</strong></td>
            <td class="p-2 text-right">{{ number_format($vente->montant_total ?? 0) }} F</td>
        </tr>
        <tr class="border-b bg-blue-100">
            <td class="p-2"><strong>Ma commission (10%)</strong></td>
            <td class="p-2 text-right text-blue-600 font-bold text-lg">
                {{ number_format($vente->commission ?? 0) }} F
            </td>
        </tr>
        <tr>
            <td class="p-2"><strong>Montant net pour l'établissement</strong></td>
            <td class="p-2 text-right text-green-600 font-bold">
                {{ number_format($vente->montant_net ?? 0) }} F
            </td>
        </tr>
    </table>
</div>
<!-- ========================================== -->
<!-- 📊 RÉCAPITULATIF COMPLET                    -->
<!-- ========================================== -->
<div class="border-2 border-blue-300 bg-blue-50 p-4 rounded-lg mb-6">
    <h4 class="font-bold text-lg text-blue-800 mb-3">📊 RÉCAPITULATIF COMPLET</h4>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <!-- Colonne 1 : Paiements -->
        <div class="bg-white p-3 rounded-lg">
            <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">💳 PAIEMENTS</h5>
            <table class="w-full text-sm">
                <tr class="border-b">
                    <td class="py-1">Acompte (25%)</td>
                    <td class="py-1 text-right font-bold text-green-600">{{ number_format($vente->acompte ?? 0) }} F</td>
                </tr>
                <tr class="border-b">
                    <td class="py-1">Solde restant</td>
                    <td class="py-1 text-right font-bold text-orange-600">{{ number_format($vente->solde ?? 0) }} F</td>
                </tr>
                <tr class="border-b">
                    <td class="py-1">Nombre de mensualités</td>
                    <td class="py-1 text-right font-bold">{{ $vente->nb_mensualites ?? 0 }} mois</td>
                </tr>
                <tr>
                    <td class="py-1">Montant par mensualité</td>
                    <td class="py-1 text-right font-bold">{{ number_format($vente->montant_mensualite ?? 0) }} F</td>
                </tr>
            </table>
        </div>
        
        <!-- Colonne 2 : Commissions -->
        <div class="bg-white p-3 rounded-lg">
            <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">💰 COMMISSIONS</h5>
            <table class="w-full text-sm">
                <tr class="border-b">
                    <td class="py-1">Montant total de la vente</td>
                    <td class="py-1 text-right">{{ number_format($vente->montant_total ?? 0) }} F</td>
                </tr>
                <tr class="border-b bg-blue-50">
                    <td class="py-1 font-bold text-blue-700">💵 Ma commission (10%)</td>
                    <td class="py-1 text-right font-bold text-blue-600">{{ number_format($vente->commission ?? 0) }} F</td>
                </tr>
                <tr>
                    <td class="py-1 font-bold text-green-700">🏦 Net établissement</td>
                    <td class="py-1 text-right font-bold text-green-600">{{ number_format($vente->montant_net ?? 0) }} F</td>
                </tr>
            </table>
        </div>
        
        <!-- Colonne 3 : Échéances -->
        <div class="bg-white p-3 rounded-lg">
            <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">📅 ÉCHÉANCES</h5>
            <table class="w-full text-sm">
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
        
        <!-- Colonne 4 : Totaux -->
        <div class="bg-white p-3 rounded-lg">
            <h5 class="font-bold text-gray-700 border-b pb-2 mb-2">📊 TOTAUX</h5>
            <table class="w-full text-sm">
                @php
                    $totalPaye = $vente->echeances()->where('statut', 'payé')->sum('montant_dû') ?? 0;
                    $totalRestant = ($vente->solde ?? 0) - $totalPaye;
                    $prochaine = $vente->echeances()->where('statut', 'en_attente')->orderBy('date_echeance')->first();
                @endphp
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
        
    </div>
</div>

        <!-- ========================================== -->
        <!-- PIED DE PAGE                                -->
        <!-- ========================================== -->
        <div class="text-center text-gray-600 text-sm mt-6 pt-4 border-t">
            <p>📱 Contact : 92108545 | 📧 gracelumineuse01@gmail.com</p>
            <p class="font-bold">Merci de votre confiance !</p>
            <p class="text-xs">Ce document fait foi de contrat de vente à crédit.</p>
        </div>

        <!-- ========================================== -->
        <!-- ACTIONS                                     -->
        <!-- ========================================== -->
        <div class="flex flex-wrap justify-end gap-2 mt-6">
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