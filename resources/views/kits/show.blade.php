@extends('layouts.app')

@section('title', 'Détails du kit')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📦 {{ $kit->nom_kit }}</h1>
    <a href="{{ route('kits.index') }}" class="text-gray-600 hover:underline">← Retour</a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <p><strong>Nom :</strong> {{ $kit->nom_kit }}</p>
    <p><strong>Description :</strong> {{ $kit->description ?? 'Aucune description' }}</p>
    <p class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($kit->prix_total, 0, ',', ' ') }} F</p>
    
    <div class="mt-6">
        <h2 class="font-bold mb-2">📋 Articles inclus :</h2>
        <ul class="list-disc list-inside">
            @foreach($kit->articles as $article)
            <li>
                {{ $article->nom_article }} 
                (x{{ $article->pivot->quantite }}) 
                - {{ number_format($article->prix_unitaire * $article->pivot->quantite, 0, ',', ' ') }} F
            </li>
            @endforeach
        </ul>
        <hr class="my-2">
        <p class="font-bold">Total : {{ number_format($kit->prix_total, 0, ',', ' ') }} F</p>
    </div>
</div>
@endsection