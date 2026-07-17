@extends('layouts.app')

@section('title', 'Nouveau kit')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Nouveau kit</h1>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('kits.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 mb-2">Nom du kit *</label>
                <input type="text" name="nom_kit" class="w-full border rounded-lg px-3 py-2" value="{{ old('nom_kit') }}" required>
                @error('nom_kit')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Description</label>
                <input type="text" name="description" class="w-full border rounded-lg px-3 py-2" value="{{ old('description') }}">
            </div>
        </div>

        <!-- Articles du kit -->
        <div class="mt-4">
            <h3 class="font-bold text-lg mb-2">📦 Articles du kit</h3>
            
            <div id="articles-container">
                <div class="flex gap-4 mb-2 article-row">
                    <select name="articles[0][id]" class="flex-1 border rounded-lg px-3 py-2" required>
                        <option value="">Sélectionner</option>
                        @foreach($articles as $a)
                        <option value="{{ $a->id }}">{{ $a->nom_article }} ({{ number_format($a->prix_vente) }} F)</option>
                        @endforeach
                    </select>
                    <input type="number" name="articles[0][quantite]" value="1" min="1" class="w-24 border rounded-lg px-3 py-2" required>
                    <button type="button" onclick="supprimerLigne(this)" class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">✕</button>
                </div>
            </div>

            <button type="button" onclick="ajouterLigne()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Ajouter un article
            </button>
        </div>

        <!-- Frais -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 mb-2">Réduction (FCFA)</label>
                <input type="number" name="reduction" id="reduction" class="w-full border rounded-lg px-3 py-2" value="{{ old('reduction', 0) }}" min="0" step="0.01">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais livraison</label>
                <input type="number" name="frais_livraison" id="frais_livraison" class="w-full border rounded-lg px-3 py-2" value="{{ old('frais_livraison', 0) }}" min="0" step="0.01">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais carnet</label>
                <input type="number" name="frais_carnet" id="frais_carnet" class="w-full border rounded-lg px-3 py-2" value="{{ old('frais_carnet', 0) }}" min="0" step="0.01">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais emballage</label>
                <input type="number" name="frais_emballage" id="frais_emballage" class="w-full border rounded-lg px-3 py-2" value="{{ old('frais_emballage', 0) }}" min="0" step="0.01">
            </div>
        </div>

        <!-- Promotion -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 mb-2">En promotion</label>
                <select name="en_promotion" class="w-full border rounded-lg px-3 py-2">
                    <option value="0">Non</option>
                    <option value="1">Oui</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Date début promo</label>
                <input type="date" name="date_debut_promo" class="w-full border rounded-lg px-3 py-2" value="{{ old('date_debut_promo') }}">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Date fin promo</label>
                <input type="date" name="date_fin_promo" class="w-full border rounded-lg px-3 py-2" value="{{ old('date_fin_promo') }}">
            </div>
        </div>

        <!-- Récapitulatif -->
        <div class="bg-gray-50 rounded-lg p-4 mt-4">
            <h4 class="font-bold text-lg mb-2">💰 Récapitulatif</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-gray-600">Prix total</span>
                    <div class="text-xl font-bold" id="prix_total_display">0 F</div>
                </div>
                <div>
                    <span class="text-gray-600">Réduction</span>
                    <div class="text-xl font-bold text-green-600" id="reduction_display">0 F</div>
                </div>
                <div>
                    <span class="text-gray-600">Prix final</span>
                    <div class="text-2xl font-bold text-blue-600" id="prix_final_display">0 F</div>
                </div>
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Enregistrer
            </button>
            <a href="{{ route('kits.index') }}" class="text-gray-600 hover:underline">Annuler</a>
        </div>
    </form>
</div>

<script>
    let articleIndex = 1;

    function ajouterLigne() {
        const container = document.getElementById('articles-container');
        const row = document.createElement('div');
        row.className = 'flex gap-4 mb-2 article-row';
        
        let options = '<option value="">Sélectionner</option>';
        @foreach($articles as $a)
            options += `<option value="{{ $a->id }}">{{ $a->nom_article }} ({{ number_format($a->prix_vente) }} F)</option>`;
        @endforeach
        
        row.innerHTML = `
            <select name="articles[${articleIndex}][id]" class="flex-1 border rounded-lg px-3 py-2" required>
                ${options}
            </select>
            <input type="number" name="articles[${articleIndex}][quantite]" value="1" min="1" class="w-24 border rounded-lg px-3 py-2" required>
            <button type="button" onclick="supprimerLigne(this)" class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">✕</button>
        `;
        
        container.appendChild(row);
        articleIndex++;
        calculerTotaux();
    }

    function supprimerLigne(btn) {
        const row = btn.closest('.article-row');
        if (document.querySelectorAll('.article-row').length > 1) {
            row.remove();
            calculerTotaux();
        } else {
            alert('Vous devez avoir au moins un article dans le kit.');
        }
    }

    function calculerTotaux() {
        const rows = document.querySelectorAll('.article-row');
        let total = 0;

        rows.forEach(row => {
            const select = row.querySelector('select');
            const quantite = parseInt(row.querySelector('input[type="number"]').value) || 1;
            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                const prix = parseFloat(option.text.match(/\(([0-9, ]+)/)?.[1]?.replace(/[, ]/g, '') || 0);
                total += prix * quantite;
            }
        });

        const reduction = parseFloat(document.getElementById('reduction').value) || 0;
        const fraisLivraison = parseFloat(document.getElementById('frais_livraison').value) || 0;
        const fraisCarnet = parseFloat(document.getElementById('frais_carnet').value) || 0;
        const fraisEmballage = parseFloat(document.getElementById('frais_emballage').value) || 0;

        const totalFinal = total - reduction + fraisLivraison + fraisCarnet + fraisEmballage;

        document.getElementById('prix_total_display').textContent = total.toLocaleString() + ' F';
        document.getElementById('reduction_display').textContent = reduction.toLocaleString() + ' F';
        document.getElementById('prix_final_display').textContent = totalFinal.toLocaleString() + ' F';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('#reduction, #frais_livraison, #frais_carnet, #frais_emballage').forEach(input => {
            input.addEventListener('input', calculerTotaux);
        });
        document.querySelectorAll('.article-row select, .article-row input[type="number"]').forEach(el => {
            el.addEventListener('change', calculerTotaux);
            el.addEventListener('input', calculerTotaux);
        });
        calculerTotaux();
    });
</script>
@endsection