@extends('layouts.app')

@section('title', 'Détails de la vente')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">🛒 Détails de la vente</h1>
    <a href="{{ route('ventes.index') }}" class="text-gray-600 hover:underline">← Retour</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Informations de la vente -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-bold text-lg mb-4">Informations générales</h2>
        <p><strong>Client :</strong> {{ $vente->client->prenom }} {{ $vente->client->nom }}</p>
        <p><strong>Téléphone :</strong> {{ $vente->client->telephone }}</p>
        <p><strong>Kit :</strong> {{ $vente->kit->nom_kit ?? 'N/A' }}</p>
        <p><strong>Date de vente :</strong> {{ $vente->date_vente->format('d/m/Y') }}</p>
        <p><strong>Statut :</strong> 
            @if($vente->statut == 'en_cours')
                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">En cours</span>
            @elseif($vente->statut == 'termine')
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Terminé</span>
            @else
                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Annulé</span>
            @endif
        </p>
    </div>
    
    <!-- Informations financières -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-bold text-lg mb-4">Informations financières</h2>
        <p><strong>Montant total :</strong> <span class="text-xl font-bold text-blue-600">{{ number_format($vente->montant_total, 0, ',', ' ') }} F</span></p>
        <p><strong>Acompte (1/4) :</strong> {{ number_format($vente->acompte, 0, ',', ' ') }} F</p>
        <p><strong>Solde restant :</strong> {{ number_format($vente->solde, 0, ',', ' ') }} F</p>
        <p><strong>Nombre de mensualités :</strong> {{ $vente->nb_mensualites }}</p>
        <p><strong>Montant par mensualité :</strong> {{ number_format($vente->montant_mensualite, 0, ',', ' ') }} F</p>
    </div>
</div>

<!-- Échéances -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h2 class="font-bold text-lg mb-4">📅 Échéances</h2>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Échéance</th>
                <th class="px-4 py-2 text-left">Montant</th>
                <th class="px-4 py-2 text-left">Statut</th>
                <th class="px-4 py-2 text-left">Date de paiement</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->echeances as $echeance)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                <td class="px-4 py-2">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                <td class="px-4 py-2">
                    @if($echeance->statut == 'en_attente')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">En attente</span>
                    @elseif($echeance->statut == 'paye')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Payé</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">En retard</span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection