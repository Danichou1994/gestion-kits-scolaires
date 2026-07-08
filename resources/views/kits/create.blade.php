@extends('layouts.app')

@section('title', 'Créer un kit')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Créer un kit</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-4xl">
    <form action="{{ route('kits.store') }}" method="POST" id="kitForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nom du kit *</label>
                <input type="text" name="nom_kit" class="w-full border rounded-lg px-3 py-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Description</label>
                <textarea name="description" class="w-full border rounded-lg px-3 py-2" rows="1"></textarea>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Articles inclus *</label>
            <div id="articles-container">
                <div class="article-row flex flex-wrap gap-2 mb-2">
                    <select name="articles[0][id]" class="flex-1 min-w-[200px] border rounded-lg px-3 py-2 article-select" required>
                        <option value="">Sélectionner</option>
                        @foreach($articles as $article)
                        <option value="{{ $article->id }}" data-prix="{{ $article->prix_vente }}">
                            {{ $article->nom_article }} ({{ number_format($article->prix_vente, 0, ',', ' ') }} F)
                        </option>
                        @endforeach
                    </select>
                    <input type="number" name="articles[0][quantite]" placeholder="Qté" class="w-20 border rounded-lg px-3 py-2 quantite-input" required min="1" value="1">
                    <button type="button" onclick="removeArticleRow(this)" class="text-red-600 hover:text-red-800 px-2">✕</button>
                </div>
            </div>
            <button type="button" onclick="addArticleRow()" class="text-blue-600 hover:underline mt-2">
                + Ajouter un article
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <div>
                <label class="block text-gray-700 mb-2">Réduction (FCFA)</label>
                <input type="number" name="reduction" id="reduction" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais livraison</label>
                <input type="number" name="frais_livraison" id="frais_livraison" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais carnet</label>
                <input type="number" name="frais_carnet" id="frais_carnet" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais emballage</label>
                <input type="number" name="frais_emballage" id="frais_emballage" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais étiquette</label>
                <input type="number" name="frais_etiquette" id="frais_etiquette" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="0">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="block text-gray-700 mb-2">En promotion</label>
                <input type="checkbox" name="en_promotion" id="en_promotion" value="1">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Date début promotion</label>
                <input type="date" name="date_debut_promo" id="date_debut_promo" class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Date fin promotion</label>
                <input type="date" name="date_fin_promo" id="date_fin_promo" class="w-full border rounded-lg px-3 py-2">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Notes</label>
            <textarea name="kit_notes" class="w-full border rounded-lg px-3 py-2" rows="2"></textarea>
        </div>

        <!-- Récapitulatif -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-bold text-lg mb-2">📊 Récapitulatif</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-gray-600">Total articles:</span>
                    <span class="font-bold" id="total_articles_display">0 F</span>
                </div>
                <div>
                    <span class="text-gray-600">Réduction:</span>
                    <span class="font-bold text-green-600" id="reduction_display">0 F</span>
                </div>
                <div>
                    <span class="text-gray-600">Frais livraison:</span>
                    <span class="font-bold" id="livraison_display">0 F</span>
                </div>
                <div>
                    <span class="text-gray-600">Frais carnet:</span>
                    <span class="font-bold" id="carnet_display">0 F</span>
                </div>
                <div>
                    <span class="text-gray-600">Frais emballage:</span>
                    <span class="font-bold" id="emballage_display">0 F</span>
                </div>
                <div>
                    <span class="text-gray-600">Frais étiquette:</span>
                    <span class="font-bold" id="etiquette_display">0 F</span>
                </div>
                <div class="col-span-2 md:col-span-3 border-t pt-2 mt-2">
                    <span class="text-xl font-bold">Prix final:</span>
                    <span class="text-2xl font-bold text-blue-600" id="prix_final_display">0 F</span>
                </div>
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Créer le kit
        </button>
        <a href="{{ route('kits.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>

<script>
    let articleIndex = 1;

    function addArticleRow() {
        const container = document.getElementById('articles-container');
        const row = document.createElement('div');
        row.className = 'article-row flex flex-wrap gap-2 mb-2';
        row.innerHTML = `
            <select name="articles[${articleIndex}][id]" class="flex-1 min-w-[200px] border rounded-lg px-3 py-2 article-select" required>
                <option value="">Sélectionner</option>
                @foreach($articles as $article)
                <option value="{{ $article->id }}" data-prix="{{ $article->prix_vente }}">
                    {{ $article->nom_article }} ({{ number_format($article->prix_vente, 0, ',', ' ') }} F)
                </option>
                @endforeach
            </select>
            <input type="number" name="articles[${articleIndex}][quantite]" placeholder="Qté" class="w-20 border rounded-lg px-3 py-2 quantite-input" required min="1" value="1">
            <button type="button" onclick="removeArticleRow(this)" class="text-red-600 hover:text-red-800 px-2">✕</button>
        `;
        container.appendChild(row);
        articleIndex++;
    }

    function removeArticleRow(button) {
        const row = button.parentElement;
        if (document.querySelectorAll('.article-row').length > 1) {
            row.remove();
            calculerPrix();
        }
    }

    function calculerPrix() {
        const rows = document.querySelectorAll('.article-row');
        let total = 0;

        rows.forEach(row => {
            const select = row.querySelector('.article-select');
            const quantite = row.querySelector('.quantite-input');
            if (select.value && quantite.value) {
                const prix = parseFloat(select.options[select.selectedIndex]?.dataset.prix || 0);
                total += prix * parseInt(quantite.value);
            }
        });

        const reduction = parseFloat(document.getElementById('reduction')?.value || 0);
        const livraison = parseFloat(document.getElementById('frais_livraison')?.value || 0);
        const carnet = parseFloat(document.getElementById('frais_carnet')?.value || 0);
        const emballage = parseFloat(document.getElementById('frais_emballage')?.value || 0);
        const etiquette = parseFloat(document.getElementById('frais_etiquette')?.value || 0);

        const prixFinal = total - reduction + livraison + carnet + emballage + etiquette;

        document.getElementById('total_articles_display').textContent = total.toLocaleString() + ' F';
        document.getElementById('reduction_display').textContent = reduction.toLocaleString() + ' F';
        document.getElementById('livraison_display').textContent = livraison.toLocaleString() + ' F';
        document.getElementById('carnet_display').textContent = carnet.toLocaleString() + ' F';
        document.getElementById('emballage_display').textContent = emballage.toLocaleString() + ' F';
        document.getElementById('etiquette_display').textContent = etiquette.toLocaleString() + ' F';
        document.getElementById('prix_final_display').textContent = prixFinal.toLocaleString() + ' F';
    }

    document.addEventListener('change', function(e) {
        if (e.target.closest('.article-select') || e.target.closest('.quantite-input')) {
            calculerPrix();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.id === 'reduction' || e.target.id === 'frais_livraison' || 
            e.target.id === 'frais_carnet' || e.target.id === 'frais_emballage' || 
            e.target.id === 'frais_etiquette') {
            calculerPrix();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        calculerPrix();
    });
</script>
@endsection