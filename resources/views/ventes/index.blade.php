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
                <th class="px-6 py-3 text-left">Client</th>
                <th class="px-6 py-3 text-left">Kit</th>
                <th class="px-6 py-3 text-left">Total</th>
                <th class="px-6 py-3 text-left">Acompte</th>
                <th class="px-6 py-3 text-left">Solde</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
            <tr class="border-t">
                <td class="px-6 py-3">{{ $vente->client->prenom }} {{ $vente->client->nom }}</td>
                <td class="px-6 py-3">{{ $vente->kit->nom_kit ?? 'N/A' }}</td>
                <td class="px-6 py-3">{{ number_format($vente->montant_total, 0, ',', ' ') }} F</td>
                <td class="px-6 py-3">{{ number_format($vente->acompte, 0, ',', ' ') }} F</td>
                <td class="px-6 py-3">{{ number_format($vente->solde, 0, ',', ' ') }} F</td>
                <td class="px-6 py-3">
                    @if($vente->statut == 'en_cours')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">En cours</span>
                    @elseif($vente->statut == 'termine')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Terminé</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Annulé</span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    <a href="{{ route('ventes.show', $vente) }}" class="text-blue-600 hover:underline mr-2">Voir</a>
                    <a href="{{ route('ventes.edit', $vente) }}" class="text-yellow-600 hover:underline mr-2">Modifier</a>
                    <form action="{{ route('ventes.destroy', $vente) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucune vente enregistrée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection