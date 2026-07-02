@extends('layouts.app')

@section('title', 'Créer un kit')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Créer un kit</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('kits.store') }}" method="POST" id="kitForm">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Nom du kit *</label>
            <input type="text" name="nom_kit" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Description</label>
            <textarea name="description" class="w-full border rounded-lg px-3 py-2" rows="2"></textarea>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Prix total (FCFA) *</label>
            <input type="number" name="prix_total" class="w-full border rounded-lg px-3 py-2" required min="0">
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Articles inclus *</label>
            <div id="articles-container">
                <div class="article-row flex space-x-2 mb-2">
                    <select name="articles[0][id]" class="w-2/3 border rounded-lg px-3 py-2" required>
                        <option value="">Sélectionner un article</option>
                        @foreach($articles as $article)
                        <option value="{{ $article->id }}">
                            {{ $article->nom_article }} ({{ number_format($article->prix_unitaire, 0, ',', ' ') }} F)
                        </option>
                        @endforeach
                    </select>
                    <input type="number" name="articles[0][quantite]" placeholder="Qté" class="w-1/3 border rounded-lg px-3 py-2" required min="1">
                    <button type="button" onclick="removeArticleRow(this)" class="text-red-600 hover:text-red-800">✕</button>
                </div>
            </div>
            <button type="button" onclick="addArticleRow()" class="text-blue-600 hover:underline mt-2">
                + Ajouter un article
            </button>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Enregistrer
        </button>
        <a href="{{ route('kits.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>

<script>
    let articleIndex = 1;
    
    function addArticleRow() {
        const container = document.getElementById('articles-container');
        const row = document.createElement('div');
        row.className = 'article-row flex space-x-2 mb-2';
        row.innerHTML = `
            <select name="articles[${articleIndex}][id]" class="w-2/3 border rounded-lg px-3 py-2" required>
                <option value="">Sélectionner un article</option>
                @foreach($articles as $article)
                <option value="{{ $article->id }}">
                    {{ $article->nom_article }} ({{ number_format($article->prix_unitaire, 0, ',', ' ') }} F)
                </option>
                @endforeach
            </select>
            <input type="number" name="articles[${articleIndex}][quantite]" placeholder="Qté" class="w-1/3 border rounded-lg px-3 py-2" required min="1">
            <button type="button" onclick="removeArticleRow(this)" class="text-red-600 hover:text-red-800">✕</button>
        `;
        container.appendChild(row);
        articleIndex++;
    }
    
    function removeArticleRow(button) {
        const row = button.parentElement;
        if (document.querySelectorAll('.article-row').length > 1) {
            row.remove();
        } else {
            alert('Vous devez avoir au moins un article dans le kit.');
        }
    }
</script>
@endsection