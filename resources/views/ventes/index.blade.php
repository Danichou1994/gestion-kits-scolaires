@extends('layouts.app')

@section('title', 'Liste des ventes')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">🛒 Liste des ventes</h1>
    <a href="{{ route('ventes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Nouvelle vente
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">N°</th>
                <th class="px-6 py-3 text-left">Client</th>
                <th class="px-6 py-3 text-left">Type</th>
                <th class="px-6 py-3 text-left">Total</th>
                <th class="px-6 py-3 text-left">Solde</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
            <tr class="border-t">
                <td class="px-6 py-3">{{ $vente->numero_vente }}</td>
                <td class="px-6 py-3">{{ $vente->client->prenom }} {{ $vente->client->nom }}</td>
                <td class="px-6 py-3">{{ $vente->type_vente == 'kit' ? '🎒 Kit' : '📦 Article' }}</td>
                <td class="px-6 py-3">{{ number_format($vente->montant_total, 0, ',', ' ') }} F</td>
                <td class="px-6 py-3">{{ number_format($vente->solde, 0, ',', ' ') }} F</td>
                <td class="px-6 py-3">
                    @if($vente->statut == 'en_cours')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">En cours</span>
                    @elseif($vente->statut == 'termine')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Terminé</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Annulé</span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    <a href="{{ route('ventes.show', $vente) }}" class="text-blue-600 hover:underline mr-2">Voir</a>
                    <a href="{{ route('ventes.facture', $vente) }}" class="text-purple-600 hover:underline mr-2" target="_blank">📄</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucune vente</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection