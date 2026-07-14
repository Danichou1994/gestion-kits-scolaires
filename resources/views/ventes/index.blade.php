@extends('layouts.app')

@section('title', 'Liste des ventes')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">🛒 Gestion des ventes</h1>
    <div class="flex gap-2">
        <a href="{{ route('ventes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Nouvelle vente
        </a>
        <a href="{{ route('ventes.export-csv') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            📥 CSV
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-blue-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Total ventes</span>
        <div class="text-2xl font-bold text-blue-600">{{ $totalVentes }}</div>
    </div>
    <div class="bg-green-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Chiffre d'affaires</span>
        <div class="text-2xl font-bold text-green-600">{{ number_format($totalChiffre, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-yellow-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">En cours</span>
        <div class="text-2xl font-bold text-yellow-600">{{ $ventesEnCours }}</div>
    </div>
    <div class="bg-red-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Solde total</span>
        <div class="text-2xl font-bold text-red-600">{{ number_format($totalSolde, 0, ',', ' ') }} F</div>
    </div>
</div>

<!-- Liste des ventes -->
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">N°</th>
                <th class="px-4 py-2 text-left">Client</th>
                <th class="px-4 py-2 text-left">Type</th>
                <th class="px-4 py-2 text-left">Produit</th>
                <th class="px-4 py-2 text-right">Total</th>
                <th class="px-4 py-2 text-right">Solde</th>
                <th class="px-4 py-2 text-center">Statut</th>
                <th class="px-4 py-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2 font-semibold">{{ $vente->numero_vente }}</td>
                <td class="px-4 py-2">{{ $vente->client->prenom }} {{ $vente->client->nom }}</td>
                <td class="px-4 py-2">
                    @if($vente->type_vente == 'mixte')
                        🛍️ Mixte
                    @elseif($vente->type_vente == 'kit')
                        🎒 Kit
                    @else
                        📦 Article
                    @endif
                </td>
                <td class="px-4 py-2">
                    @if($vente->type_vente == 'mixte')
                        <span class="text-xs text-gray-500">Multi-produits</span>
                    @elseif($vente->type_vente == 'kit')
                        {{ $vente->kit->nom_kit ?? 'N/A' }}
                    @else
                        {{ $vente->article->nom_article ?? 'N/A' }}
                    @endif
                </td>
                <td class="px-4 py-2 text-right font-semibold">{{ number_format($vente->montant_total, 0, ',', ' ') }} F</td>
                <td class="px-4 py-2 text-right {{ $vente->solde > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format($vente->solde, 0, ',', ' ') }} F
                </td>
                <td class="px-4 py-2 text-center">
                    @if($vente->statut == 'en_cours')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">⏳ En cours</span>
                    @elseif($vente->statut == 'termine')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">✅ Terminé</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">❌ Annulé</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-center">
                    <div class="flex flex-wrap justify-center gap-1">
                        <a href="{{ route('ventes.show', $vente) }}" class="text-blue-600 hover:underline text-sm" title="Voir">📋</a>
                        <a href="{{ route('ventes.facture', $vente) }}" target="_blank" class="text-purple-600 hover:underline text-sm" title="Facture">📄</a>
                        @if($vente->statut == 'en_cours')
                            <a href="{{ route('ventes.edit', $vente) }}" class="text-yellow-600 hover:underline text-sm" title="Modifier">✏️</a>
                        @endif
                        <form action="{{ route('ventes.destroy', $vente) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Supprimer cette vente ? Les échéances seront aussi supprimées.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm" title="Supprimer">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">Aucune vente enregistrée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection