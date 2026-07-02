@extends('layouts.app')

@section('title', 'Modifier une échéance')

@section('content')
<h1 class="text-2xl font-bold mb-6">✏️ Modifier une échéance</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ route('echeances.update', $echeance) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Vente</label>
            <select name="vente_id" class="w-full border rounded-lg px-3 py-2" required>
                @foreach($ventes as $vente)
                <option value="{{ $vente->id }}" @if($vente->id == $echeance->vente_id) selected @endif>
                    {{ $vente->client->prenom }} {{ $vente->client->nom }} - 
                    {{ number_format($vente->montant_total, 0, ',', ' ') }} F
                </option>
                @endforeach
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Montant dû (FCFA) *</label>
            <input type="number" name="montant_dû" value="{{ old('montant_dû', $echeance->montant_dû) }}" class="w-full border rounded-lg px-3 py-2" required min="0">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Date d'échéance *</label>
            <input type="date" name="date_echeance" value="{{ old('date_echeance', $echeance->date_echeance->format('Y-m-d')) }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Statut *</label>
            <select name="statut" class="w-full border rounded-lg px-3 py-2" required>
                <option value="en_attente" @if($echeance->statut == 'en_attente') selected @endif>En attente</option>
                <option value="paye" @if($echeance->statut == 'paye') selected @endif>Payé</option>
                <option value="en_retard" @if($echeance->statut == 'en_retard') selected @endif>En retard</option>
            </select>
        </div>
        
        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
            Mettre à jour
        </button>
        <a href="{{ route('echeances.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection