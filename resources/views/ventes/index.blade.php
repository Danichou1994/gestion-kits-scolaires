@extends('layouts.app')

@section('title', 'Gestion des ventes')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">🛒 Gestion des ventes</h1>
    <a href="{{ route('ventes.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Nouvelle vente
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-blue-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Total ventes</span>
        <div class="text-2xl font-bold text-blue-600">{{ $totalVentes ?? 0 }}</div>
    </div>
    <div class="bg-green-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Chiffre d'affaires</span>
        <div class="text-2xl font-bold text-green-600">{{ number_format($totalChiffre ?? 0) }} F</div>
    </div>
    <div class="bg-yellow-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Solde total</span>
        <div class="text-2xl font-bold text-yellow-600">{{ number_format($totalSolde ?? 0) }} F</div>
    </div>
    <div class="bg-red-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Ventes en cours</span>
        <div class="text-2xl font-bold text-red-600">{{ $ventesEnCours ?? 0 }}</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">N°</th>
                <th class="px-4 py-3 text-left">Client</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-right">Commission</th>
                <th class="px-4 py-3 text-right">Solde</th>
                <th class="px-4 py-3 text-center">Mode</th>
                <th class="px-4 py-3 text-center">Statut</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3">{{ $vente->numero_vente }}</td>
                <td class="px-4 py-3">{{ $vente->client->prenom ?? '' }} {{ $vente->client->nom ?? '' }}</td>
                <td class="px-4 py-3">{{ $vente->date_vente->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-right font-bold">{{ number_format($vente->montant_total) }} F</td>
                <td class="px-4 py-3 text-right text-blue-600">{{ number_format($vente->commission ?? 0) }} F</td>
                <td class="px-4 py-3 text-right text-red-600">{{ number_format($vente->solde) }} F</td>
                <td class="px-4 py-3 text-center">
                    @if($vente->mode_paiement == 'especes')
                        <span class="text-green-600">💵</span>
                    @elseif($vente->mode_paiement == 'mobile_money')
                        <span class="text-blue-600">📱</span>
                    @else
                        <span class="text-yellow-600">🔄</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    @if($vente->statut == 'en_cours')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">⏳ En cours</span>
                    @elseif($vente->statut == 'termine')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✅ Terminé</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">❌ Annulé</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('ventes.show', $vente) }}" class="text-blue-600 hover:text-blue-800">👁️</a>
                        <a href="{{ route('ventes.edit', $vente) }}" class="text-green-600 hover:text-green-800">✏️</a>
                        <a href="{{ route('ventes.facture', $vente) }}" class="text-purple-600 hover:text-purple-800">📄</a>
                        <form action="{{ route('ventes.destroy', $vente) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette vente ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-6 text-center text-gray-500">Aucune vente enregistrée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection