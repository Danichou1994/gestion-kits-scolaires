@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">📊 Tableau de bord</h1>
    <p class="text-gray-600">Bienvenue sur votre plateforme de gestion</p>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Clients</div>
        <div class="text-3xl font-bold">{{ $totalClients ?? 0 }}</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Ventes</div>
        <div class="text-3xl font-bold">{{ $totalVentes ?? 0 }}</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Chiffre d'affaires</div>
        <div class="text-2xl font-bold">{{ number_format($chiffreAffaires ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Échéances aujourd'hui</div>
        <div class="text-3xl font-bold">{{ $echeancesAujourdhui ?? 0 }}</div>
    </div>
</div>

<!-- Deuxième ligne -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Bénéfice total</div>
        <div class="text-2xl font-bold">{{ number_format($beneficeTotal ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Valeur du stock</div>
        <div class="text-2xl font-bold">{{ number_format($valeurStock ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Articles en alerte</div>
        <div class="text-3xl font-bold">{{ $articlesAlerte ?? 0 }}</div>
    </div>
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Ventes du mois</div>
        <div class="text-3xl font-bold">{{ $ventesMois ?? 0 }}</div>
    </div>
</div>

<!-- Actions rapides -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <a href="{{ route('clients.create') }}" class="bg-blue-100 hover:bg-blue-200 rounded-xl p-4 text-center transition">
        <div class="text-3xl">👤</div>
        <div class="font-semibold text-blue-700 text-sm">Nouveau client</div>
    </a>
    <a href="{{ route('ventes.create') }}" class="bg-green-100 hover:bg-green-200 rounded-xl p-4 text-center transition">
        <div class="text-3xl">🛒</div>
        <div class="font-semibold text-green-700 text-sm">Nouvelle vente</div>
    </a>
    <a href="{{ route('articles.create') }}" class="bg-purple-100 hover:bg-purple-200 rounded-xl p-4 text-center transition">
        <div class="text-3xl">📦</div>
        <div class="font-semibold text-purple-700 text-sm">Nouvel article</div>
    </a>
    <a href="{{ route('stock.index') }}" class="bg-yellow-100 hover:bg-yellow-200 rounded-xl p-4 text-center transition">
        <div class="text-3xl">📊</div>
        <div class="font-semibold text-yellow-700 text-sm">Gestion stock</div>
    </a>
    <a href="{{ route('echeances.index') }}" class="bg-red-100 hover:bg-red-200 rounded-xl p-4 text-center transition">
        <div class="text-3xl">📅</div>
        <div class="font-semibold text-red-700 text-sm">Échéances</div>
    </a>
</div>

<!-- Top articles -->
@if(isset($topArticles) && $topArticles->count() > 0)
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4">🏆 Top 5 des articles les plus rentables</h2>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Article</th>
                <th class="px-4 py-2 text-left">Bénéfice unitaire</th>
                <th class="px-4 py-2 text-left">Stock</th>
                <th class="px-4 py-2 text-left">Bénéfice total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topArticles as $article)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $article->nom_article }}</td>
                <td class="px-4 py-2 text-green-600">{{ number_format($article->benefice, 0, ',', ' ') }} F</td>
                <td class="px-4 py-2">{{ $article->stock }}</td>
                <td class="px-4 py-2 font-bold">{{ number_format($article->benefice * $article->stock, 0, ',', ' ') }} F</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection