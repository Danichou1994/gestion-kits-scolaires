@extends('layouts.app')

@section('title', 'Ajouter un client')

@section('content')
<h1 class="text-2xl font-bold mb-6">➕ Ajouter un client</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Nom *</label>
            <input type="text" name="nom" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Prénom *</label>
            <input type="text" name="prenom" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Téléphone *</label>
            <input type="text" name="telephone" class="w-full border rounded-lg px-3 py-2" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Adresse</label>
            <input type="text" name="adresse" class="w-full border rounded-lg px-3 py-2">
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Quartier</label>
            <input type="text" name="quartier" class="w-full border rounded-lg px-3 py-2">
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
            Enregistrer
        </button>
        <a href="{{ route('clients.index') }}" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
@endsection