@extends('layouts.app')

@section('title', 'Nouvelle vente')

@section('content')
<h1 class="text-2xl font-bold mb-6">🛒 Nouvelle vente</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('ventes.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Client *</label>
            <select name="client_id" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Sélectionner un client</option>
                @foreach($clients as $client)
                <option value="{{ $client->id }}">
                    {{ $client->prenom }} {{ $client->nom }} - {{ $client->telephone }}
                </option>
                @endforeach
            </select>
            <a href="{{ route('clients.create') }}" class="text-blue-600 hover:underline text-sm">+ Ajouter un nouveau client</a>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Kit *</label>
            <select name="kit_id" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Sélectionner un kit</option>
                @foreach($kits as $kit)
                <option value="{{ $kit->id }}">
                    {{ $kit->nom_kit }} - {{ number_format($kit->prix_total, 0, ',', ' ') }} F
                </option>
                @endforeach
            </select>
            <a href="{{ route('kits.create') }}" class="text-blue-600 hover:underline text-sm">+ Créer un nouveau kit</a>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Montant total (FCFA) *</label>
            <input type="number" name="montant_total" class="w-full border rounded-lg px-3 py-2" required min="0" step="100">
            <p class="text-sm text-gray-500 mt-1">Le montant sera automatiquement divisé : 1/4 en acompte, 3/4 en mensualités.</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Nombre de mensualités *</label>
            <select name="nb_mensualites" class="w-full border rounded-lg px-3 py-2" required>
                <option value="2">2 mensualités</option>
                <option value="3" selected>3 mensualités</option>
                <option value="4">4 mensualités</option>
                <option value="5">5 mensualités</option>
                <option value="6">6 mensualités</option>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Date de la vente *</label>
            <input type="date" name="date_vente" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Enregistrer la vente
        </button>
        <a href="{{ route('ventes.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>

<script>
    // Remplir automatiquement la date du jour
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('input[name="date_vente"]');
        if (dateInput) {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            dateInput.value = year + '-' + month + '-' + day;
        }
    });
</script>
@endsection