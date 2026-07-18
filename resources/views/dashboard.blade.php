@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">📊 Tableau de bord</h1>
    <p class="text-gray-600">Bienvenue sur votre plateforme de gestion</p>
</div>

<!-- ========================================== -->
<!-- 1. STATISTIQUES GÉNÉRALES                   -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">👥 Clients</div>
        <div class="text-3xl font-bold">{{ $totalClients ?? 0 }}</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">📦 Ventes</div>
        <div class="text-3xl font-bold">{{ $totalVentes ?? 0 }}</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">💰 Chiffre d'affaires</div>
        <div class="text-2xl font-bold">{{ number_format($chiffreAffaires ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">📅 Échéances aujourd'hui</div>
        <div class="text-3xl font-bold">{{ $echeancesAujourdhui ?? 0 }}</div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. FINANCES & COMMISSIONS                  -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">💰 Total ventes</div>
        <div class="text-2xl font-bold">{{ number_format($chiffreAffaires ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow p-6 text-white">
    <div class="text-sm opacity-80">💵 Ma commission (10%)</div>
    <div class="text-2xl font-bold">{{ number_format($totalCommission ?? 0, 0, ',', ' ') }} F</div>
</div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">🏦 Net établissement</div>
        <div class="text-2xl font-bold">{{ number_format($totalNet ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">📆 Commission ce mois</div>
        <div class="text-2xl font-bold">{{ number_format($commissionMois ?? 0, 0, ',', ' ') }} F</div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. BÉNÉFICE & STOCK                        -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">💰 Bénéfice total</div>
        <div class="text-2xl font-bold">{{ number_format($beneficeTotal ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">📦 Valeur du stock</div>
        <div class="text-2xl font-bold">{{ number_format($valeurStock ?? 0, 0, ',', ' ') }} F</div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">⚠️ Articles en alerte</div>
        <div class="text-3xl font-bold">{{ $articlesAlerte ?? 0 }}</div>
    </div>
    <div class="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">📆 Ventes du mois</div>
        <div class="text-3xl font-bold">{{ $ventesMois ?? 0 }}</div>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. ÉCHÉANCES EN RETARD                     -->
<!-- ========================================== -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">⚠️ Échéances en retard</div>
        <div class="text-3xl font-bold">{{ $echeancesRetard ?? 0 }}</div>
    </div>
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow p-6 text-white">
        <div class="text-sm opacity-80">📦 Total articles</div>
        <div class="text-3xl font-bold">{{ \App\Models\Article::count() }}</div>
    </div>
</div>

<!-- ========================================== -->
<!-- 5. ACTIONS RAPIDES                         -->
<!-- ========================================== -->
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

<!-- ========================================== -->
<!-- 6. TOP 5 ARTICLES                          -->
<!-- ========================================== -->
@if(isset($topArticles) && $topArticles->count() > 0)
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4">🏆 Top 5 des articles les plus rentables</h2>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Article</th>
                <th class="px-4 py-2 text-left">Catégorie</th>
                <th class="px-4 py-2 text-right">Bénéfice unitaire</th>
                <th class="px-4 py-2 text-right">Stock</th>
                <th class="px-4 py-2 text-right">Bénéfice total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topArticles as $article)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-2">{{ $article->nom_article }}</td>
                <td class="px-4 py-2">{{ $article->categorie ?? '-' }}</td>
                <td class="px-4 py-2 text-right text-green-600">
                    @if(($article->benefice ?? 0) > 0)
                        {{ number_format($article->benefice, 0, ',', ' ') }} F
                    @else
                        <span class="text-red-500">0 F</span>
                    @endif
                </td>
                <td class="px-4 py-2 text-right">{{ $article->stock ?? 0 }}</td>
                <td class="px-4 py-2 text-right font-bold text-blue-600">
                    @if(($article->benefice ?? 0) > 0)
                        {{ number_format(($article->benefice ?? 0) * ($article->stock ?? 0), 0, ',', ' ') }} F
                    @else
                        0 F
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<!-- ========================================== -->
<!-- 7. MESSAGE SI AUCUN ARTICLE                -->
<!-- ========================================== -->
@if(!isset($topArticles) || $topArticles->count() == 0)
<div class="bg-white rounded-xl shadow p-6 text-center">
    <p class="text-gray-500">Aucun article trouvé. Commencez par ajouter des articles !</p>
    <a href="{{ route('articles.create') }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        + Ajouter un article
    </a>
</div>
@endif

@endsection