@extends('layouts.app')

@section('title', 'Liste des articles')

@section('content')
<div class="flex flex-wrap justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📦 Liste des articles</h1>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('articles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Ajouter
        </a>
        <a href="{{ route('articles.export-csv') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            📥 Exporter CSV
        </a>
        <button onclick="document.getElementById('importForm').classList.toggle('hidden')" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
            📤 Importer CSV
        </button>
    </div>
</div>

<!-- Formulaire d'import -->
<div id="importForm" class="hidden bg-white rounded-lg shadow p-4 mb-6">
    <form action="{{ route('articles.import-csv') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="flex gap-4 items-center">
            <input type="file" name="fichier" accept=".csv" required>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Importer
            </button>
        </div>
    </form>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-blue-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Total articles</span>
        <div class="text-2xl font-bold text-blue-600">{{ $nbArticles }}</div>
    </div>
    <div class="bg-green-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Stock total</span>
        <div class="text-2xl font-bold text-green-600">{{ $stockTotal }}</div>
    </div>
    <div class="bg-purple-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Bénéfice total</span>
        <div class="text-2xl font-bold text-purple-600">{{ number_format($beneficeTotal, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-yellow-100 p-4 rounded-lg">
        <span class="text-sm text-gray-600">Valeur stock</span>
        <div class="text-2xl font-bold text-yellow-600">{{ number_format($valeurStock, 0, ',', ' ') }} F</div>
    </div>
</div>

<!-- Liste des articles -->
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Nom</th>
                <th class="px-4 py-2 text-left">Catégorie</th>
                <th class="px-4 py-2 text-right">Prix achat</th>
                <th class="px-4 py-2 text-right">Prix vente</th>
                <th class="px-4 py-2 text-right">Bénéfice</th>
                <th class="px-4 py-2 text-right">Stock</th>
                <th class="px-4 py-2 text-left">Statut</th>
                <th class="px-4 py-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $article)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">
                    <div class="font-semibold">{{ $article->nom_article }}</div>
                    <div class="text-xs text-gray-500">{{ $article->code_barre ?? 'Sans code' }}</div>
                </td>
                <td class="px-4 py-2 text-sm">{{ $article->categorie }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($article->prix_achat, 0, ',', ' ') }}</td>
                <td class="px-4 py-2 text-right text-blue-600 font-semibold">{{ number_format($article->prix_vente, 0, ',', ' ') }}</td>
                <td class="px-4 py-2 text-right {{ $article->benefice >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($article->benefice, 0, ',', ' ') }}
                </td>
                <td class="px-4 py-2 text-right font-semibold">{{ $article->stock }}</td>
                <td class="px-4 py-2">
                    @if($article->stock <= $article->seuil_alerte)
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">⚠️ Alerte</span>
                    @else
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">✅ OK</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    <a href="{{ route('articles.show', $article) }}" class="text-blue-600 hover:underline text-sm">Voir</a>
                    <a href="{{ route('articles.edit', $article) }}" class="text-yellow-600 hover:underline text-sm ml-2">Modifier</a>
                    <form action="{{ route('articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-sm ml-2">Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500">Aucun article</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection