@extends('layouts.app')

@section('title', 'Nouvelle vente')

@section('content')
<h1 class="text-2xl font-bold mb-6">🛒 Nouvelle vente</h1>

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('ventes.store') }}" method="POST" id="venteForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 mb-2">Client *</label>
                <select name="client_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Sélectionner</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->prenom }} {{ $client->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Mode de paiement *</label>
               <select name="mode_paiement" id="mode_paiement" class="w-full border rounded-lg px-3 py-2" required>
               <option value="tontine" {{ old('mode_paiement') == 'tontine' ? 'selected' : '' }}>🔄 Tontine (10% commission)</option>
               <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>💵 Comptant (sans commission)</option>
               <option value="mobile_money" {{ old('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
               </select>
               <p class="text-sm text-gray-500 mt-1">⚠️ La commission de 10% s'applique uniquement pour les paiements en tontine</p>
            </div>
        </div>

        <!-- Sélection des articles et kits -->
        <div class="mt-4">
            <h3 class="font-bold text-lg mb-2">📦 Sélectionner des produits</h3>
            
            <div class="flex flex-wrap gap-4 mb-4">
                <select id="type_select" class="border rounded-lg px-3 py-2">
                    <option value="article">Article</option>
                    <option value="kit">Kit</option>
                </select>
                
                <select id="item_select" class="flex-1 min-w-[200px] border rounded-lg px-3 py-2">
                    <option value="">Sélectionner un produit</option>
                </select>
                
                <input type="number" id="quantite_select" value="1" min="1" class="w-20 border rounded-lg px-3 py-2">
                
                <button type="button" onclick="ajouterItem()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Ajouter
                </button>
            </div>

            <!-- Liste des items sélectionnés -->
            <div class="border rounded-lg p-4 mb-4">
                <h4 class="font-bold mb-2">📋 Produits sélectionnés</h4>
                <div id="items_list" class="space-y-2">
                    <p class="text-gray-500 text-sm">Aucun produit sélectionné</p>
                </div>
            </div>
        </div>

        <!-- Paramètres de paiement -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
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
                <label class="block text-gray-700 mb-2">Date vente</label>
                <input type="date" name="date_vente" class="w-full border rounded-lg px-3 py-2" value="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Remise (FCFA)</label>
                <input type="number" name="remise" id="remise" class="w-full border rounded-lg px-3 py-2" min="0" value="0" step="0.01">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais livraison</label>
                <input type="number" name="frais_livraison" id="frais_livraison" class="w-full border rounded-lg px-3 py-2" min="0" value="0" step="0.01">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais carnet</label>
                <input type="number" name="frais_carnet" id="frais_carnet" class="w-full border rounded-lg px-3 py-2" min="0" value="0" step="0.01">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 mb-2">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2" rows="2"></textarea>
        </div>

        <!-- Récapitulatif des prix -->
        <div class="bg-gray-50 rounded-lg p-4 mt-4">
            <h4 class="font-bold text-lg mb-2">💰 Récapitulatif</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-gray-600">Sous-total</span>
                    <div class="text-xl font-bold" id="sous_total_display">0 F</div>
                </div>
                <div>
                    <span class="text-gray-600">Remise</span>
                    <div class="text-xl font-bold text-green-600" id="remise_display">0 F</div>
                </div>
                <div>
                    <span class="text-gray-600">Frais livraison</span>
                    <div class="text-xl font-bold" id="livraison_display">0 F</div>
                </div>
                <div>
                    <span class="text-gray-600">Total</span>
                    <div class="text-2xl font-bold text-blue-600" id="total_final_display">0 F</div>
                </div>
            </div>
        </div>

        <input type="hidden" name="items" id="items_input">

        <div class="flex justify-between mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Enregistrer la vente
            </button>
            <a href="{{ route('ventes.index') }}" class="text-gray-600 hover:underline">Annuler</a>
        </div>
    </form>
</div>

<script>
    let items = [];
    let itemsIndex = 0;

    const articles = @json($articles);
    const kits = @json($kits);

    document.getElementById('type_select').addEventListener('change', function() {
        const type = this.value;
        const select = document.getElementById('item_select');
        select.innerHTML = '<option value="">Sélectionner un produit</option>';
        
        const data = type === 'article' ? articles : kits;
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            const prix = type === 'article' ? item.prix_vente : item.prix_final;
            option.textContent = item.nom_article || item.nom_kit + ' (' + (prix || 0).toLocaleString() + ' F)';
            option.dataset.prix = prix || 0;
            option.dataset.nom = item.nom_article || item.nom_kit;
            select.appendChild(option);
        });
    });
    document.getElementById('type_select').dispatchEvent(new Event('change'));

    function ajouterItem() {
        const type = document.getElementById('type_select').value;
        const select = document.getElementById('item_select');
        const quantite = parseInt(document.getElementById('quantite_select').value) || 1;
        
        if (!select.value) {
            alert('Veuillez sélectionner un produit');
            return;
        }

        const option = select.options[select.selectedIndex];
        const item = {
            id: itemsIndex++,
            type: type,
            id_produit: parseInt(select.value),
            nom: option.dataset.nom,
            prix: parseFloat(option.dataset.prix),
            quantite: quantite,
            total: parseFloat(option.dataset.prix) * quantite
        };

        items.push(item);
        afficherItems();
        calculerTotaux();
        
        document.getElementById('quantite_select').value = 1;
    }

    function supprimerItem(id) {
        items = items.filter(item => item.id !== id);
        afficherItems();
        calculerTotaux();
    }

    function afficherItems() {
        const container = document.getElementById('items_list');
        if (items.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm">Aucun produit sélectionné</p>';
            return;
        }

        let html = '';
        items.forEach(item => {
            const icon = item.type === 'kit' ? '🎒' : '📦';
            html += `
                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                    <div>
                        <span class="font-semibold">${icon} ${item.nom}</span>
                        <span class="text-sm text-gray-600">x${item.quantite}</span>
                        <span class="text-sm text-blue-600 ml-2">${item.total.toLocaleString()} F</span>
                    </div>
                    <button type="button" onclick="supprimerItem(${item.id})" class="text-red-600 hover:text-red-800">✕</button>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function calculerTotaux() {
        const sousTotal = items.reduce((sum, item) => sum + item.total, 0);
        const remise = parseFloat(document.getElementById('remise').value) || 0;
        const fraisLivraison = parseFloat(document.getElementById('frais_livraison').value) || 0;
        const fraisCarnet = parseFloat(document.getElementById('frais_carnet').value) || 0;
        
        // 🔥 NOUVEAU CALCUL : Commission 10% UNIQUEMENT pour tontine
        const modePaiement = document.getElementById('mode_paiement').value;
        let commission = 0;
        if (modePaiement === 'tontine') {
            commission = sousTotal * 0.10;
        }
        
        const totalFinal = sousTotal - remise + commission + fraisLivraison + fraisCarnet;

        document.getElementById('sous_total_display').textContent = sousTotal.toLocaleString() + ' F';
        document.getElementById('remise_display').textContent = remise.toLocaleString() + ' F';
        document.getElementById('livraison_display').textContent = fraisLivraison.toLocaleString() + ' F';
        document.getElementById('total_final_display').textContent = totalFinal.toLocaleString() + ' F';

        const itemsData = items.map(item => ({
            type: item.type,
            id: item.id_produit,
            quantite: item.quantite
        }));
        document.getElementById('items_input').value = JSON.stringify(itemsData);
    }

    // Écouter les changements du mode de paiement
    document.getElementById('mode_paiement').addEventListener('change', calculerTotaux);
    document.getElementById('remise').addEventListener('input', calculerTotaux);
    document.getElementById('frais_livraison').addEventListener('input', calculerTotaux);
    document.getElementById('frais_carnet').addEventListener('input', calculerTotaux);

    document.addEventListener('DOMContentLoaded', function() {
        calculerTotaux();
    });
</script>
@endsection