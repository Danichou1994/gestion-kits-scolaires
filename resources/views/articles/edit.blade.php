@extends('layouts.app')

@section('title', 'Modifier un article')

@section('content')
<h1 class="text-2xl font-bold mb-6">✏️ Modifier un article</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ route('articles.update', $article) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Nom de l'article *</label>
            <input type="text" name="nom_article" value="{{ old('nom_article', $article->nom_article) }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Prix unitaire (FCFA) *</label>
            <input type="number" name="prix_unitaire" value="{{ old('prix_unitaire', $article->prix_unitaire) }}" class="w-full border rounded-lg px-3 py-2" required min="0">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Catégorie *</label>
            <select name="categorie" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Sélectionner une catégorie</option>
                <option value="Cahiers" @if($article->categorie == 'Cahiers') selected @endif>Cahiers</option>
                <option value="Stylos" @if($article->categorie == 'Stylos') selected @endif>Stylos</option>
                <option value="Crayons" @if($article->categorie == 'Crayons') selected @endif>Crayons</option>
                <option value="Géométrie" @if($article->categorie == 'Géométrie') selected @endif>Géométrie</option>
                <option value="Accessoires" @if($article->categorie == 'Accessoires') selected @endif>Accessoires</option>
                <option value="Calculatrices" @if($article->categorie == 'Calculatrices') selected @endif>Calculatrices</option>
                <option value="Sacs" @if($article->categorie == 'Sacs') selected @endif>Sacs</option>
                <option value="Autres" @if($article->categorie == 'Autres') selected @endif>Autres</option>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Quantité en stock *</label>
            <input type="number" name="stock" value="{{ old('stock', $article->stock) }}" class="w-full border rounded-lg px-3 py-2" required min="0">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Seuil d'alerte</label>
            <input type="number" name="seuil_alerte" value="{{ old('seuil_alerte', $article->seuil_alerte) }}" class="w-full border rounded-lg px-3 py-2" min="0">
        </div>
        
        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
            Mettre à jour
        </button>
        <a href="{{ route('articles.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection