@extends('layouts.app')

@section('title', 'Liste des kits')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📦 Liste des kits</h1>
    <a href="{{ route('kits.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Créer un kit
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($kits as $kit)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-2">{{ $kit->nom_kit }}</h2>
        <p class="text-gray-600 mb-2">{{ $kit->description ?? 'Aucune description' }}</p>
        <p class="text-2xl font-bold text-blue-600 mb-4">{{ number_format($kit->prix_total, 0, ',', ' ') }} F</p>
        
        <div class="mb-4">
            <p class="font-semibold">Articles inclus :</p>
            <ul class="list-disc list-inside text-sm text-gray-600">
                @foreach($kit->articles as $article)
                <li>{{ $article->nom_article }} (x{{ $article->pivot->quantite }})</li>
                @endforeach
            </ul>
        </div>
        
        <div class="flex space-x-2">
            <a href="{{ route('kits.show', $kit) }}" class="text-blue-600 hover:underline">Voir</a>
            <a href="{{ route('kits.edit', $kit) }}" class="text-yellow-600 hover:underline">Modifier</a>
            <form action="{{ route('kits.destroy', $kit) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce kit ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center text-gray-500 py-8">
        Aucun kit créé
    </div>
    @endforelse
</div>
@endsection