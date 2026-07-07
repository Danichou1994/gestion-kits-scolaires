@extends('layouts.app')

@section('title', 'Ajouter un article')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Ajouter un article</h1>

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('articles.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Informations principales -->
            <div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nom de l'article *</label>
                    <input type="text" name="nom_article" class="w-full border rounded-lg px-3 py-2" value="{{ old('nom_article') }}" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Code barre</label>
                    <input type="text" name="code_barre" class="w-full border rounded-lg px-3 py-2" value="{{ old('code_barre') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Catégorie *</label>
                    <select name="categorie" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('categorie') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Prix -->
            <div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Prix d'achat (FCFA) *</label>
                    <input type="number" name="prix_achat" class="w-full border rounded-lg px-3 py-2" value="{{ old('prix_achat') }}" step="0.01" min="0" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Prix de vente (FCFA) *</label>
                    <input type="number" name="prix_vente" class="w-full border rounded-lg px-3 py-2" value="{{ old('prix_vente') }}" step="0.01" min="0" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Bénéfice estimé (FCFA)</label>
                    <input type="text" class="w-full border rounded-lg px-3 py-2 bg-gray-100" readonly id="benefice_estime">
                </div>
            </div>

            <!-- Stock -->
            <div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Quantité en stock *</label>
                    <input type="number" name="stock" class="w-full border rounded-lg px-3 py-2" value="{{ old('stock') }}" min="0" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Seuil d'alerte</label>
                    <input type="number" name="seuil_alerte" class="w-full border rounded-lg px-3 py-2" value="{{ old('seuil_alerte', 5) }}" min="0">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Unité de mesure *</label>
                    <select name="unite_mesure" class="w-full border rounded-lg px-3 py-2" required>
                        @foreach($unites as $unite)
                            <option value="{{ $unite }}" {{ old('unite_mesure') == $unite ? 'selected' : '' }}>{{ $unite }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Fournisseur</label>
                    <input type="text" name="fournisseur" class="w-full border rounded-lg px-3 py-2" value="{{ old('fournisseur') }}">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Emplacement</label>
                    <input type="text" name="emplacement" class="w-full border rounded-lg px-3 py-2" value="{{ old('emplacement') }}" placeholder="Ex: Étagère A1">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Poids (kg)</label>
                    <input type="number" name="poids" class="w-full border rounded-lg px-3 py-2" value="{{ old('poids') }}" step="0.01" min="0">
                </div>
            </div>
        </div>

        <!-- Marque et Description -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Marque</label>
                <input type="text" name="marque" class="w-full border rounded-lg px-3 py-2" value="{{ old('marque') }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Description</label>
                <textarea name="description" class="w-full border rounded-lg px-3 py-2" rows="2">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="flex justify-between items-center mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Enregistrer
            </button>
            <a href="{{ route('articles.index') }}" class="text-gray-600 hover:underline">Annuler</a>
        </div>
    </form>
</div>

<script>
    // Calcul automatique du bénéfice
    document.addEventListener('DOMContentLoaded', function() {
        const prixAchat = document.querySelector('input[name="prix_achat"]');
        const prixVente = document.querySelector('input[name="prix_vente"]');
        const benefice = document.getElementById('benefice_estime');

        function calculerBenefice() {
            const achat = parseFloat(prixAchat.value) || 0;
            const vente = parseFloat(prixVente.value) || 0;
            benefice.value = (vente - achat).toFixed(2);
        }

        prixAchat.addEventListener('input', calculerBenefice);
        prixVente.addEventListener('input', calculerBenefice);
    });
</script>
@endsection