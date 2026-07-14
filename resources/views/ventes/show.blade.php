@extends('layouts.app')

@section('title', 'Détail de la vente')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">🧾 Facture #{{ $vente->numero_vente }}</h1>
    <p class="text-gray-600">Détail de la vente du {{ $vente->date_vente->format('d/m/Y') }}</p>
</div>

<div class="bg-white rounded-xl shadow-lg p-6 max-w-4xl mx-auto">
    
    <!-- En-tête -->
    <div class="text-center border-b-2 border-blue-600 pb-4 mb-6">
        <h1 class="text-2xl font-bold text-blue-600">LA GRÂCE LUMINEUSE</h1>
        <p class="text-gray-600">Lomé - Togo</p>
        <p>📞 92108545 | 📧 gracelumineuse01@gmail.com</p>
    </div>

    <!-- Infos facture -->
    <div class="flex justify-between mb-4">
        <div>
            <strong>FACTURE N°</strong> {{ $vente->numero_vente }}
        </div>
        <div>
            <strong>Date</strong> {{ $vente->date_vente->format('d/m/Y') }}
        </div>
    </div>

    <!-- Infos client -->
    <div class="bg-gray-50 p-4 rounded-lg mb-6">
        <h3 class="font-bold text-lg">👤 CLIENT</h3>
        <p><strong>{{ $vente->client->prenom }} {{ $vente->client->nom }}</strong></p>
        <p>📞 {{ $vente->client->telephone }}</p>
        @if($vente->client->quartier)
            <p>📍 {{ $vente->client->quartier }}</p>
        @endif
    </div>

    <!-- Détail commande -->
    <h3 class="font-bold text-lg mb-3">📦 DÉTAIL DE LA COMMANDE</h3>
    
    @if($vente->kit)
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
            <tr class="border-b">
                <td class="p-2">{{ $vente->kit->nom_kit ?? 'Kit' }}</td>
                <td class="p-2 text-center">{{ $vente->quantite ?? 1 }}</td>
                <td class="p-2 text-right">{{ number_format($vente->kit->prix_final ?? 0) }} F</td>
                <td class="p-2 text-right">{{ number_format($vente->montant_total) }} F</td>
            </tr>
        </tbody>
    </table>
    @else
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
            <tr class="border-b">
                <td class="p-2">{{ $vente->article->nom_article ?? 'Article' }}</td>
                <td class="p-2 text-center">{{ $vente->quantite ?? 1 }}</td>
                <td class="p-2 text-right">{{ number_format($vente->article->prix_vente ?? 0) }} F</td>
                <td class="p-2 text-right">{{ number_format($vente->montant_total) }} F</td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- Totaux -->
    <table class="w-full mb-4">
        <tr>
            <td class="p-2 text-right font-bold">Sous-total</td>
            <td class="p-2 text-right">{{ number_format($vente->sous_total ?? $vente->montant_total) }} F</td>
        </tr>
        @if($vente->remise > 0)
        <tr>
            <td class="p-2 text-right">Réduction</td>
            <td class="p-2 text-right text-red-600">- {{ number_format($vente->remise) }} F</td>
        </tr>
        @endif
        @if($vente->frais_livraison > 0)
        <tr>
            <td class="p-2 text-right">Frais livraison</td>
            <td class="p-2 text-right">{{ number_format($vente->frais_livraison) }} F</td>
        </tr>
        @endif
        @if($vente->frais_carnet > 0)
        <tr>
            <td class="p-2 text-right">Frais carnet</td>
            <td class="p-2 text-right">{{ number_format($vente->frais_carnet) }} F</td>
        </tr>
        @endif
        <tr class="bg-blue-50 font-bold">
            <td class="p-2 text-right text-lg">TOTAL À PAYER</td>
            <td class="p-2 text-right text-lg text-blue-600">{{ number_format($vente->montant_total) }} F</td>
        </tr>
    </table>

    <!-- Mode de paiement -->
    <h3 class="font-bold text-lg mb-3">💳 MODE DE PAIEMENT : TONTINE</h3>
    <table class="w-full mb-4">
        <tr>
            <td class="p-2"><strong>Acompte (25%)</strong></td>
            <td class="p-2 text-right">{{ number_format($vente->acompte) }} F</td>
            <td class="p-2 text-green-600 font-bold">✅ PAYÉ</td>
        </tr>
        <tr>
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

    <!-- Échéances -->
    <h3 class="font-bold text-lg mb-3">📅 ÉCHÉANCES</h3>
    <table class="w-full border-collapse mb-6">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">N°</th>
                <th class="p-2 text-left">Date</th>
                <th class="p-2 text-right">Montant</th>
                <th class="p-2 text-center">Statut</th>
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
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ========================================== -->
    <!-- === MA COMMISSION (SECTION AJOUTÉE) === -->
    <!-- ========================================== -->
    <div class="bg-blue-50 border-2 border-blue-200 p-4 rounded-lg mb-6">
        <h4 class="font-bold text-lg text-blue-800 mb-2">💰 MA COMMISSION</h4>
        <table class="w-full">
            <tr>
                <td class="p-2"><strong>Montant total de la vente</strong></td>
                <td class="p-2 text-right">{{ number_format($vente->montant_total) }} F</td>
            </tr>
            <tr class="border-t">
                <td class="p-2"><strong>Ma commission (10%)</strong></td>
                <td class="p-2 text-right text-blue-600 font-bold">{{ number_format($vente->commission ?? 0) }} F</td>
            </tr>
            <tr class="border-t">
                <td class="p-2"><strong>Montant net pour l'établissement</strong></td>
                <td class="p-2 text-right text-green-600 font-bold">{{ number_format($vente->montant_net ?? 0) }} F</td>
            </tr>
        </table>
    </div>

    <!-- Récapitulatif -->
    <div class="border-t-2 border-blue-600 pt-4 mt-4">
        <table class="w-full">
            <tr>
                <td class="p-2"><strong>Total payé aujourd'hui</strong></td>
                <td class="p-2 text-right font-bold text-green-600">{{ number_format($vente->acompte) }} F</td>
            </tr>
            <tr>
                <td class="p-2"><strong>Reste à payer</strong></td>
                <td class="p-2 text-right font-bold text-red-600">{{ number_format($totalRestant ?? $vente->solde) }} F</td>
            </tr>
            <tr>
                <td class="p-2"><strong>Prochaine échéance</strong></td>
                <td class="p-2 text-right">
                    @if(isset($prochaineEcheance) && $prochaineEcheance)
                        {{ \Carbon\Carbon::parse($prochaineEcheance->date_echeance)->format('d/m/Y') }}
                        ({{ number_format($prochaineEcheance->montant_dû) }} F)
                    @else
                        <span class="text-green-600">✅ Aucune échéance en attente</span>
                    @endif
                </td>
            </tr>
            <tr class="border-t">
                <td class="p-2"><strong>Total commission</strong></td>
                <td class="p-2 text-right font-bold text-blue-600">{{ number_format($vente->commission ?? 0) }} F</td>
            </tr>
        </table>
    </div>

    <!-- Pied de page -->
    <div class="text-center text-gray-600 text-sm mt-6 pt-4 border-t">
        <p>📱 Contact : 92108545 | 📧 gracelumineuse01@gmail.com</p>
        <p class="font-bold">Merci de votre confiance !</p>
        <p class="text-xs">Ce document fait foi de contrat de vente à crédit.</p>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-2 mt-6">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            🖨️ Imprimer
        </button>
        <a href="{{ route('ventes.facture', $vente) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            📄 Télécharger PDF
        </a>
        <a href="{{ route('ventes.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Retour
        </a>
    </div>

</div>
@endsection