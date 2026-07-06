@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">📊 Tableau de bord</h1>
    <p class="text-gray-600">Bienvenue sur votre plateforme de gestion</p>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-blue-500 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Clients</div>
        <div class="text-3xl font-bold">{{ $totalClients ?? 0 }}</div>
    </div>
    <div class="bg-green-500 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Ventes</div>
        <div class="text-3xl font-bold">{{ $totalVentes ?? 0 }}</div>
    </div>
    <div class="bg-yellow-500 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Chiffre d'affaires</div>
        <div class="text-2xl font-bold">{{ number_format($chiffreAffaires ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-red-500 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Échéances aujourd'hui</div>
        <div class="text-3xl font-bold">{{ $echeancesAujourdhui ?? 0 }}</div>
    </div>
</div>

<!-- Deuxième ligne -->
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-purple-500 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Bénéfice total</div>
        <div class="text-2xl font-bold">{{ number_format($beneficeTotal ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-indigo-500 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Valeur du stock</div>
        <div class="text-2xl font-bold">{{ number_format($valeurStock ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-orange-500 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">Articles en alerte</div>
        <div class="text-3xl font-bold">{{ $articlesAlerte ?? 0 }}</div>
    </div>
</div>

<!-- Actions rapides -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <a href="{{ route('clients.create') }}" class="bg-blue-100 hover:bg-blue-200 rounded-xl p-4 text-center">
        <div class="text-3xl">👤</div>
        <div class="font-semibold text-blue-700">Nouveau client</div>
    </a>
    <a href="{{ route('ventes.create') }}" class="bg-green-100 hover:bg-green-200 rounded-xl p-4 text-center">
        <div class="text-3xl">🛒</div>
        <div class="font-semibold text-green-700">Nouvelle vente</div>
    </a>
    <a href="{{ route('articles.create') }}" class="bg-purple-100 hover:bg-purple-200 rounded-xl p-4 text-center">
        <div class="text-3xl">📦</div>
        <div class="font-semibold text-purple-700">Nouvel article</div>
    </a>
    <a href="{{ route('stock.index') }}" class="bg-yellow-100 hover:bg-yellow-200 rounded-xl p-4 text-center">
        <div class="text-3xl">📊</div>
        <div class="font-semibold text-yellow-700">Gestion stock</div>
    </a>
</div>
@endsection