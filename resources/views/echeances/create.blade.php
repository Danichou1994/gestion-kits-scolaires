@extends('layouts.app')

@section('title', 'Ajouter une échéance')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Ajouter une échéance</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ route('echeances.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Vente *</label>
            <select name="vente_id" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Sélectionner une vente</option>
                @foreach($ventes as $vente)
                <option value="{{ $vente->id }}">
                    {{ $vente->client->prenom }} {{ $vente->client->nom }} - 
                    {{ number_format($vente->montant_total, 0, ',', ' ') }} F
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Montant dû (FCFA) *</label>
            <input type="number" name="montant_dû" class="w-full border rounded-lg px-3 py-2" required min="0">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Date d'échéance *</label>
            <input type="date" name="date_echeance" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Enregistrer
        </button>
        <a href="{{ route('echeances.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>

<script>
    // Remplir automatiquement avec la date d'aujourd'hui + 1 mois
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('input[name="date_echeance"]');
        if (dateInput) {
            const today = new Date();
            today.setMonth(today.getMonth() + 1);
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            dateInput.value = year + '-' + month + '-' + day;
        }
    });
</script>
@endsection