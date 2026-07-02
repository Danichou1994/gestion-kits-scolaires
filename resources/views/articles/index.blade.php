@extends('layouts.app')

@section('title', 'Liste des articles')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📦 Liste des articles</h1>
    <a href="{{ route('articles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Ajouter un article
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Nom</th>
                <th class="px-6 py-3 text-left">Prix unitaire</th>
                <th class="px-6 py-3 text-left">Catégorie</th>
                <th class="px-6 py-3 text-left">Stock</th>
                <th class="px-6 py-3 text-left">Seuil d'alerte</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $article)
            <tr class="border-t">
                <td class="px-6 py-3">{{ $article->nom_article }}</td>
                <td class="px-6 py-3">{{ number_format($article->prix_unitaire, 0, ',', ' ') }} F</td>
                <td class="px-6 py-3">{{ $article->categorie }}</td>
                <td class="px-6 py-3 @if($article->stock <= $article->seuil_alerte) text-red-600 font-bold @endif">
                    {{ $article->stock }}
                    @if($article->stock <= $article->seuil_alerte)
                        ⚠️
                    @endif
                </td>
                <td class="px-6 py-3">{{ $article->seuil_alerte }}</td>
                <td class="px-6 py-3">
                    <a href="{{ route('articles.show', $article) }}" class="text-blue-600 hover:underline mr-2">Voir</a>
                    <a href="{{ route('articles.edit', $article) }}" class="text-yellow-600 hover:underline mr-2">Modifier</a>
                    <form action="{{ route('articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun article enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection