@extends('layouts.app')

@section('title', 'Détails du client')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">👤 {{ $client->prenom }} {{ $client->nom }}</h1>
    <a href="{{ route('clients.index') }}" class="text-gray-600 hover:underline">← Retour</a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <p><strong>Nom :</strong> {{ $client->nom }}</p>
    <p><strong>Prénom :</strong> {{ $client->prenom }}</p>
    <p><strong>Téléphone :</strong> {{ $client->telephone }}</p>
    <p><strong>Adresse :</strong> {{ $client->adresse ?? 'Non renseignée' }}</p>
    <p><strong>Quartier :</strong> {{ $client->quartier ?? 'Non renseigné' }}</p>
    
    <div class="mt-6">
        <h2 class="font-bold mb-2">📊 Historique des ventes</h2>
        @if($client->ventes->count() > 0)
            <ul>
            @foreach($client->ventes as $vente)
                <li class="border-b py-2">
                    Vente du {{ $vente->date_vente->format('d/m/Y') }} - 
                    {{ number_format($vente->montant_total, 0, ',', ' ') }} F
                    ({{ $vente->statut }})
                </li>
            @endforeach
            </ul>
        @else
            <p class="text-gray-500">Aucune vente enregistrée</p>
        @endif
    </div>
</div>
@endsection