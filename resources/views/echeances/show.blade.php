@extends('layouts.app')

@section('title', 'Détails de l\'échéance')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📅 Détails de l'échéance</h1>
    <a href="{{ route('echeances.index') }}" class="text-gray-600 hover:underline">← Retour</a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <p><strong>Client :</strong> {{ $echeance->client->prenom }} {{ $echeance->client->nom }}</p>
    <p><strong>Téléphone :</strong> {{ $echeance->client->telephone }}</p>
    <p><strong>Vente associée :</strong> 
        <a href="{{ route('ventes.show', $echeance->vente) }}" class="text-blue-600 hover:underline">
            Vente #{{ $echeance->vente->id }}
        </a>
    </p>
    <p><strong>Montant dû :</strong> <span class="text-xl font-bold text-blue-600">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</span></p>
    <p><strong>Date d'échéance :</strong> {{ $echeance->date_echeance->format('d/m/Y') }}</p>
    <p><strong>Statut :</strong> 
        @if($echeance->statut == 'en_attente')
            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">En attente</span>
        @elseif($echeance->statut == 'paye')
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Payé</span>
        @else
            <span class="bg-red-100 text-red-800 px-2 py-1 rounded">En retard</span>
        @endif
    </p>
    <p><strong>Date de paiement :</strong> {{ $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : 'Non payé' }}</p>
</div>
@endsection