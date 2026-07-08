@extends('layouts.app')

@section('title', 'Liste des kits')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📦 Liste des kits</h1>
    <div class="flex gap-2">
        <a href="{{ route('kits.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Créer un kit
        </a>
        <a href="{{ route('kits.export-csv') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            📥 CSV
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($kits as $kit)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-2">{{ $kit->nom_kit }}</h2>
        <p class="text-gray-600 text-sm mb-2">{{ $kit->description ?? 'Aucune description' }}</p>
        
        <div class="mb-4">
            <p class="text-sm text-gray-600">Articles inclus :</p>
            <ul class="text-sm list-disc list-inside">
                @foreach($kit->articles as $article)
                <li>{{ $article->nom_article }} (x{{ $article->pivot->quantite }})</li>
                @endforeach
            </ul>
        </div>

        <div class="border-t pt-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Total HT:</span>
                <span>{{ number_format($kit->prix_total, 0, ',', ' ') }} F</span>
            </div>
            @if($kit->reduction > 0)
            <div class="flex justify-between text-green-600">
                <span>Réduction:</span>
                <span>-{{ number_format($kit->reduction, 0, ',', ' ') }} F</span>
            </div>
            @endif
            <div class="flex justify-between text-xl font-bold text-blue-600">
                <span>Prix final:</span>
                <span>{{ number_format($kit->prix_final, 0, ',', ' ') }} F</span>
            </div>
            @if($kit->estEnPromotion)
            <div class="text-red-600 text-sm font-bold">🔥 En promotion !</div>
            @endif
        </div>

        <div class="flex gap-2 mt-4">
            <a href="{{ route('kits.show', $kit) }}" class="text-blue-600 hover:underline text-sm">Voir</a>
            <a href="{{ route('kits.edit', $kit) }}" class="text-yellow-600 hover:underline text-sm">Modifier</a>
            <form action="{{ route('kits.destroy', $kit) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:underline text-sm">Supprimer</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 text-center text-gray-500 py-8">Aucun kit créé</div>
    @endforelse
</div>
@endsection