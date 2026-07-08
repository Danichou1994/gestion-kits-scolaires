@extends('layouts.app')

@section('title', 'Détails de la vente')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">🛒 Détails de la vente</h1>
    <div class="flex gap-2">
        <a href="{{ route('ventes.index') }}" class="text-gray-600 hover:underline">← Retour</a>
        <a href="{{ route('ventes.facture', $vente) }}" target="_blank" 
           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
            📄 Télécharger la facture
        </a>
    </div>
</div>

<!-- En-tête -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-blue-600">{{ $vente->numero_vente }}</h2>
            <p class="text-gray-600">Date: {{ $vente->date_vente->format('d/m/Y') }}</p>
        </div>
        <div>
            {!! $vente->statut_badge !!}
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Client -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-lg mb-4">👤 Client</h3>
        <p><strong>Nom:</strong> {{ $vente->client->prenom }} {{ $vente->client->nom }}</p>
        <p><strong>Téléphone:</strong> {{ $vente->client->telephone }}</p>
        <p><strong>Email:</strong> {{ $vente->client->email ?? 'Non renseigné' }}</p>
        <p><strong>Quartier:</strong> {{ $vente->client->quartier ?? 'Non renseigné' }}</p>
    </div>

    <!-- Produit -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-lg mb-4">📦 Produit</h3>
        <p><strong>Type:</strong> {{ $vente->type_vente == 'kit' ? '🎒 Kit' : '📦 Article' }}</p>
        @if($vente->type_vente == 'kit')
            <p><strong>Nom:</strong> {{ $vente->kit->nom_kit ?? 'N/A' }}</p>
            <p><strong>Description:</strong> {{ $vente->kit->description ?? 'Aucune' }}</p>
        @else
            <p><strong>Nom:</strong> {{ $vente->article->nom_article ?? 'N/A' }}</p>
            <p><strong>Quantité:</strong> {{ $vente->quantite }}</p>
            <p><strong>Prix unitaire:</strong> {{ number_format($vente->article->prix_vente ?? 0, 0, ',', ' ') }} F</p>
        @endif
    </div>
</div>

<!-- Détails financiers -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="font-bold text-lg mb-4">💰 Détails financiers</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <span class="text-gray-600">Total</span>
            <div class="text-xl font-bold text-blue-600">{{ number_format($vente->montant_total, 0, ',', ' ') }} F</div>
        </div>
        <div>
            <span class="text-gray-600">Acompte (1/4)</span>
            <div class="text-xl font-bold text-green-600">{{ number_format($vente->acompte, 0, ',', ' ') }} F</div>
        </div>
        <div>
            <span class="text-gray-600">Solde</span>
            <div class="text-xl font-bold text-red-600">{{ number_format($vente->solde, 0, ',', ' ') }} F</div>
        </div>
        <div>
            <span class="text-gray-600">Mensualités</span>
            <div class="text-xl font-bold">{{ $vente->nb_mensualites }} x {{ number_format($vente->montant_mensualite, 0, ',', ' ') }} F</div>
        </div>
    </div>
</div>

<!-- Échéances -->
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="font-bold text-lg mb-4">📅 Échéances</h3>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">#</th>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-right">Montant</th>
                <th class="px-4 py-2 text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->echeances as $index => $echeance)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $index + 1 }}</td>
                <td class="px-4 py-2">{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                <td class="px-4 py-2 text-center">
                    @if($echeance->statut == 'paye')
                        <span class="text-green-600">✅ Payé</span>
                    @elseif($echeance->statut == 'en_attente')
                        <span class="text-yellow-600">⏳ En attente</span>
                    @else
                        <span class="text-red-600">⚠️ En retard</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection