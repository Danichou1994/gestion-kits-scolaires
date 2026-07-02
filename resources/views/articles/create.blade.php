@extends('layouts.app')

@section('title', 'Ajouter un article')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Ajouter un article</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ route('articles.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Nom de l'article *</label>
            <input type="text" name="nom_article" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Prix unitaire (FCFA) *</label>
            <input type="number" name="prix_unitaire" class="w-full border rounded-lg px-3 py-2" required min="0">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Catégorie *</label>
            <select name="categorie" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Sélectionner une catégorie</option>
                <option value="Cahiers">Cahiers</option>
                <option value="Stylos">Stylos</option>
                <option value="Crayons">Crayons</option>
                <option value="Géométrie">Géométrie</option>
                <option value="Accessoires">Accessoires</option>
                <option value="Calculatrices">Calculatrices</option>
                <option value="Sacs">Sacs</option>
                <option value="Autres">Autres</option>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Quantité en stock *</label>
            <input type="number" name="stock" class="w-full border rounded-lg px-3 py-2" required min="0">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Seuil d'alerte</label>
            <input type="number" name="seuil_alerte" class="w-full border rounded-lg px-3 py-2" value="10" min="0">
            <p class="text-sm text-gray-500 mt-1">Quand le stock atteint ce nombre, une alerte s'affiche.</p>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Enregistrer
        </button>
        <a href="{{ route('articles.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection