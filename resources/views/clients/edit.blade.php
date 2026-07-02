@extends('layouts.app')

@section('title', 'Modifier un client')

@section('content')
<h1 class="text-2xl font-bold mb-6">✏️ Modifier un client</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ route('clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Nom *</label>
            <input type="text" name="nom" value="{{ old('nom', $client->nom) }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Prénom *</label>
            <input type="text" name="prenom" value="{{ old('prenom', $client->prenom) }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Téléphone *</label>
            <input type="text" name="telephone" value="{{ old('telephone', $client->telephone) }}" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Adresse</label>
            <input type="text" name="adresse" value="{{ old('adresse', $client->adresse) }}" class="w-full border rounded-lg px-3 py-2">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Quartier</label>
            <input type="text" name="quartier" value="{{ old('quartier', $client->quartier) }}" class="w-full border rounded-lg px-3 py-2">
        </div>
        
        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
            Mettre à jour
        </button>
        <a href="{{ route('clients.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection