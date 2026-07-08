@extends('layouts.app')

@section('title', 'Nouvelle vente')

@section('content')
<h1 class="text-2xl font-bold mb-6">🛒 Nouvelle vente</h1>

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('ventes.store') }}" method="POST" id="venteForm">
        @csrf

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

        <!-- Liste des articles/kits -->
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Articles / Kits *</label>
            <div id="items-container">
                <div class="item-row flex flex-wrap gap-2 mb-2">
                    <select name="items[0][type]" class="item-type w-32 border rounded-lg px-3 py-2">
                        <option value="article">📦 Article</option>
                        <option value="kit">🎒 Kit</option>
                    </select>
                    <select name="items[0][id]" class="item-select flex-1 min-w-[200px] border rounded-lg px-3 py-2" required>
                        <option value="">Sélectionner</option>
                        @foreach($articles as $article)
                        <option value="{{ $article->id }}" data-type="article" data-prix="{{ $article->prix_vente }}" data-stock="{{ $article->stock }}">
                            {{ $article->nom_article }} ({{ number_format($article->prix_vente, 0, ',', ' ') }} F) - Stock: {{ $article->stock }}
                        </option>
                        @endforeach
                        @foreach($kits as $kit)
                        <option value="{{ $kit->id }}" data-type="kit" data-prix="{{ $kit->prix_final }}">
                            🎒 {{ $kit->nom_kit }} ({{ number_format($kit->prix_final, 0, ',', ' ') }} F)
                        </option>
                        @endforeach
                    </select>
                    <input type="number" name="items[0][quantite]" placeholder="Qté" class="item-qte w-20 border rounded-lg px-3 py-2" required min="1" value="1">
                    <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800 px-2">✕</button>
                </div>
            </div>
            <button type="button" onclick="addItem()" class="text-blue-600 hover:underline mt-2">
                + Ajouter un article/kit
            </button>
        </div>

        <!-- Frais -->
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
                <input type="number" name="remise" id="remise" class="w-full border rounded-lg px-3 py-2" min="0" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais livraison</label>
                <input type="number" name="frais_livraison" id="frais_livraison" class="w-full border rounded-lg px-3 py-2" min="0" value="0">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais carnet</label>
                <input type="number" name="frais_carnet" id="frais_carnet" class="w-full border rounded-lg px-3 py-2" min="0" value="0">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2" rows="2"></textarea>
        </div>

        <!-- Récapitulatif -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
           