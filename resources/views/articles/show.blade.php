@extends('layouts.app')

@section('title', 'Détails de l\'article')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📦 {{ $article->nom_article }}</h1>
    <a href="{{ route('articles.index') }}" class="text-gray-600 hover:underline">← Retour</a>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <p><strong>Nom :</strong> {{ $article->nom_article }}</p>
    <p><strong>Prix unitaire :</strong> {{ number_format($article->prix_unitaire, 0, ',', ' ') }} F</p>
    <p><strong>Catégorie :</strong> {{ $article->categorie }}</p>
    <p><strong>Stock :</strong> {{ $article->stock }}</p>
    <p><strong>Seuil d'alerte :</strong> {{ $article->seuil_alerte }}</p>
    <p><strong>Statut :</strong> 
        @if($article->stock <= $article->seuil_alerte)
            <span class="text-red-600 font-bold">⚠️ Stock bas !</span>
        @else
            <span class="text-green-600">✅ Stock suffisant</span>
        @endif
    </p>
</div>
@endsection