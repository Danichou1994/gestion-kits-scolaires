@extends('layouts.app')

@section('title', 'Gestion de stock')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📊 Gestion de stock</h1>
    <a href="{{ route('stock.export-csv') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        📥 Exporter CSV
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Formulaire de mouvement -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-bold text-lg mb-4">➕ Nouveau mouvement</h2>
        <form action="{{ route('stock.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Article *</label>
                <select name="article_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Sélectionner</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}">
                            {{ $article->nom_article }} (Stock: {{ $article->stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Type *</label>
                <select name="type_mouvement" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="entree">📥 Entrée</option>
                    <option value="sortie">📤 Sortie</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Quantité *</label>
                <input type="number" name="quantite" class="w-full border rounded-lg px-3 py-2" required min="1">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Prix unitaire</label>
                <input type="number" name="prix_unitaire" class="w-full border rounded-lg px-3 py-2" step="0.01" min="0">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Motif</label>
                <input type="text" name="motif" class="w-full border rounded-lg px-3 py-2" placeholder="Ex: Réapprovisionnement">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full">
                Enregistrer le mouvement
            </button>
        </form>
    </div>

    <!-- Derniers mouvements -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-bold text-lg mb-4">📋 Derniers mouvements</h2>
        <div class="max-h-96 overflow-y-auto">
            @forelse($stocks as $stock)
                <div class="border-b py-2 flex justify-between">
                    <div>
                        <span class="font-semibold">{{ $stock->article->nom_article }}</span>
                        <span class="text-sm text-gray-600 block">{{ $stock->motif ?? 'Aucun motif' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-bold {{ $stock->type == 'entree' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stock->type == 'entree' ? '+' : '-' }}{{ $stock->quantite }}
                        </span>
                        <span class="text-xs text-gray-500 block">{{ $stock->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Aucun mouvement</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Tableau des stocks -->
<div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
    <h2 class="font-bold text-lg p-4 border-b">📦 État des stocks</h2>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Article</th>
                <th class="px-4 py-2 text-left">Stock</th>
                <th class="px-4 py-2 text-left">Seuil</th>
                <th class="px-4 py-2 text-left">Statut</th>
                <th class="px-4 py-2 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $article->nom_article }}</td>
                <td class="px-4 py-2 font-bold">{{ $article->stock }}</td>
                <td class="px-4 py-2">{{ $article->seuil_alerte }}</td>
                <td class="px-4 py-2">
                    @if($article->stock <= $article->seuil_alerte)
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">⚠️ Alerte</span>
                    @else
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✅ OK</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    <a href="{{ route('stock.historique', $article) }}" class="text-blue-600 hover:underline text-sm">Historique</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection