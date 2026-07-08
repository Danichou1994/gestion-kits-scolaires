@extends('layouts.app')

@section('title', 'Nouvelle vente')

@section('content')
<h1 class="text-2xl font-bold mb-6">🛒 Nouvelle vente</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-4xl">
    <form action="{{ route('ventes.store') }}" method="POST" id="venteForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Client -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Client *</label>
                <select name="client_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Sélectionner</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->prenom }} {{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type de vente -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Type de vente *</label>
                <select name="type_vente" id="type_vente" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Sélectionner</option>
                    <option value="kit">🎒 Kit</option>
                    <option value="article">📦 Article</option>
                </select>
            </div>

            <!-- Kit -->
            <div class="mb-4" id="kit_section" style="display:none;">
                <label class="block text-gray-700 mb-2">Kit *</label>
                <select name="kit_id" id="kit_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Sélectionner</option>
                    @foreach($kits as $kit)
                    <option value="{{ $kit->id }}" data-prix="{{ $kit->prix_final }}">
                        {{ $kit->nom_kit }} ({{ number_format($kit->prix_final, 0, ',', ' ') }} F)
                    </option>
                    @endforeach
                </select>
                <div class="mt-2 text-sm text-gray-500">Le prix est automatique</div>
            </div>

            <!-- Article -->
            <div class="mb-4" id="article_section" style="display:none;">
                <label class="block text-gray-700 mb-2">Article *</label>
                <select name="article_id" id="article_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Sélectionner</option>
                    @foreach($articles as $article)
                    <option value="{{ $article->id }}" data-prix="{{ $article->prix_vente }}" data-stock="{{ $article->stock }}">
                        {{ $article->nom_article }} ({{ number_format($article->prix_vente, 0, ',', ' ') }} F) - Stock: {{ $article->stock }}
                    </option>
                    @endforeach
                </select>
                <div class="mt-2 text-sm text-gray-500">Le prix est automatique</div>
            </div>

            <!-- Quantité (pour article) -->
            <div class="mb-4" id="quantite_section" style="display:none;">
                <label class="block text-gray-700 mb-2">Quantité *</label>
                <input type="number" name="quantite" id="quantite" class="w-full border rounded-lg px-3 py-2" min="1" value="1">
            </div>
        </div>

        <!-- Montant total (auto-calculé) -->
        <div class="bg-blue-50 p-4 rounded-lg mb-4">
            <div class="flex justify-between items-center">
                <span class="text-lg font-bold">💰 Montant total</span>
                <span class="text-2xl font-bold text-blue-600" id="montant_total_display">0 F</span>
            </div>
            <input type="hidden" name="montant_total" id="montant_total_input" value="0">
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 mb-2">Mensualités</label>
                <select name="nb_mensualites" class="w-full border rounded-lg px-3 py-2">
                    <option value="2">2</option>
                    <option value="3" selected>3</option>
                    <option value="4">4</option>
                    <option value="6">6</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Mode paiement</label>
                <select name="mode_paiement" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Sélectionner</option>
                    <option value="especes">Espèces</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="cheque">Chèque</option>
                    <option value="virement">Virement</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Date vente</label>
                <input type="date" name="date_vente" class="w-full border rounded-lg px-3 py-2" value="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Remise (FCFA)</label>
                <input type="number" name="remise" class="w-full border rounded-lg px-3 py-2" min="0" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais livraison</label>
                <input type="number" name="frais_livraison" class="w-full border rounded-lg px-3 py-2" min="0" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais carnet</label>
                <input type="number" name="frais_carnet" class="w-full border rounded-lg px-3 py-2" min="0" value="0">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2" rows="2"></textarea>
        </div>

        <div class="flex justify-between mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Enregistrer la vente
            </button>
            <a href="{{ route('ventes.index') }}" class="text-gray-600 hover:underline">Annuler</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeVente = document.getElementById('type_vente');
        const kitSection = document.getElementById('kit_section');
        const articleSection = document.getElementById('article_section');
        const quantiteSection = document.getElementById('quantite_section');
        const kitSelect = document.getElementById('kit_id');
        const articleSelect = document.getElementById('article_id');
        const quantiteInput = document.getElementById('quantite');
        const montantDisplay = document.getElementById('montant_total_display');
        const montantInput = document.getElementById('montant_total_input');

        function calculerMontant() {
            let total = 0;
            const type = typeVente.value;

            if (type === 'kit') {
                const selected = kitSelect.options[kitSelect.selectedIndex];
                const prix = parseFloat(selected?.dataset.prix || 0);
                total = prix;
            } else if (type === 'article') {
                const selected = articleSelect.options[articleSelect.selectedIndex];
                const prix = parseFloat(selected?.dataset.prix || 0);
                const qte = parseInt(quantiteInput.value) || 0;
                total = prix * qte;
            }

            // Ajouter frais supplémentaires
            const fraisLivraison = parseFloat(document.querySelector('input[name="frais_livraison"]').value) || 0;
            const fraisCarnet = parseFloat(document.querySelector('input[name="frais_carnet"]').value) || 0;
            const remise = parseFloat(document.querySelector('input[name="remise"]').value) || 0;

            total = total + fraisLivraison + fraisCarnet - remise;

            montantDisplay.textContent = total.toLocaleString() + ' F';
            montantInput.value = total;
        }

        typeVente.addEventListener('change', function() {
            const value = this.value;
            kitSection.style.display = value === 'kit' ? 'block' : 'none';
            articleSection.style.display = value === 'article' ? 'block' : 'none';
            quantiteSection.style.display = value === 'article' ? 'block' : 'none';
            calculerMontant();
        });

        kitSelect.addEventListener('change', calculerMontant);
        articleSelect.addEventListener('change', calculerMontant);
        quantiteInput.addEventListener('input', calculerMontant);
        document.querySelectorAll('input[name="frais_livraison"], input[name="frais_carnet"], input[name="remise"]').forEach(el => {
            el.addEventListener('input', calculerMontant);
        });
    });
</script>
@endsection