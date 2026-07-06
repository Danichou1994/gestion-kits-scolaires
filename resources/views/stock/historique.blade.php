@extends('layouts.app')

@section('title', 'Historique - ' . $article->nom_article)

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📊 Historique de {{ $article->nom_article }}</h1>
    <a href="{{ route('stock.index') }}" class="text-gray-600 hover:underline">← Retour</a>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded">
            <span class="text-sm text-gray-600">Stock actuel</span>
            <div class="text-2xl font-bold text-blue-600">{{ $article->stock }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded">
            <span class="text-sm text-gray-600">Prix d'achat</span>
            <div class="text-2xl font-bold text-green-600">{{ number_format($article->prix_achat, 0, ',', ' ') }} F</div>
        </div>
        <div class="bg-purple-50 p-4 rounded">
            <span class="text-sm text-gray-600">Prix de vente</span>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($article->prix_vente, 0, ',', ' ') }} F</div>
        </div>
    </div>

    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Type</th>
                <th class="px-4 py-2 text-left">Quantité</th>
                <th class="px-4 py-2 text-left">Avant</th>
                <th class="px-4 py-2 text-left">Après</th>
                <th class="px-4 py-2 text-left">Motif</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $stock->created_at->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-2">
                    <span class="{{ $stock->type == 'entree' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stock->type == 'entree' ? '📥 Entrée' : '📤 Sortie' }}
                    </span>
                </td>
                <td class="px-4 py-2 font-bold">{{ $stock->quantite }}</td>
                <td class="px-4 py-2">{{ $stock->stock_avant }}</td>
                <td class="px-4 py-2">{{ $stock->stock_apres }}</td>
                <td class="px-4 py-2">{{ $stock->motif ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection