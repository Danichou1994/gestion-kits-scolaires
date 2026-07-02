@extends('layouts.app')

@section('title', 'Tableau de bord - Gestion Kits Scolaires')

@section('content')
<!-- En-tête avec bienvenue -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">📊 Tableau de bord</h1>
    <p class="text-gray-600 mt-1">Bienvenue sur votre plateforme de gestion des kits scolaires</p>
</div>

<!-- Statistiques avec couleurs -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
    <!-- Clients -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-80">Clients</div>
                <div class="text-3xl font-bold">{{ $totalClients }}</div>
            </div>
            <div class="text-4xl opacity-80">👥</div>
        </div>
    </div>
    
    <!-- Ventes -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-80">Ventes</div>
                <div class="text-3xl font-bold">{{ $totalVentes }}</div>
            </div>
            <div class="text-4xl opacity-80">🛒</div>
        </div>
    </div>
    
    <!-- Chiffre d'affaires -->
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-80">Chiffre d'affaires</div>
                <div class="text-3xl font-bold">{{ number_format($chiffreAffaires, 0, ',', ' ') }} F</div>
            </div>
            <div class="text-4xl opacity-80">💰</div>
        </div>
    </div>
    
    <!-- Paiements à recevoir -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-80">Paiements aujourd'hui</div>
                <div class="text-3xl font-bold">{{ $echeancesAujourdhui }}</div>
            </div>
            <div class="text-4xl opacity-80">📅</div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="mb-8">
    <h2 class="text-xl font-bold text-gray-700 mb-4">⚡ Actions rapides</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('clients.create') }}" class="bg-blue-50 hover:bg-blue-100 rounded-xl p-4 text-center transition duration-300 border-2 border-blue-200 hover:border-blue-400">
            <div class="text-3xl mb-2">👤</div>
            <div class="font-semibold text-blue-700">Nouveau client</div>
        </a>
        <a href="{{ route('ventes.create') }}" class="bg-green-50 hover:bg-green-100 rounded-xl p-4 text-center transition duration-300 border-2 border-green-200 hover:border-green-400">
            <div class="text-3xl mb-2">🛒</div>
            <div class="font-semibold text-green-700">Nouvelle vente</div>
        </a>
        <a href="{{ route('articles.create') }}" class="bg-purple-50 hover:bg-purple-100 rounded-xl p-4 text-center transition duration-300 border-2 border-purple-200 hover:border-purple-400">
            <div class="text-3xl mb-2">📦</div>
            <div class="font-semibold text-purple-700">Nouvel article</div>
        </a>
        <a href="{{ route('echeances.index') }}" class="bg-red-50 hover:bg-red-100 rounded-xl p-4 text-center transition duration-300 border-2 border-red-200 hover:border-red-400">
            <div class="text-3xl mb-2">📅</div>
            <div class="font-semibold text-red-700">Voir échéances</div>
        </a>
    </div>
</div>

<!-- Rappels du jour -->
<div class="bg-white rounded-xl shadow-lg p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-700">🔔 Rappels du jour</h2>
        <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
            {{ $echeancesAujourdhui }} paiement(s)
        </span>
    </div>
    
    @php
        $echeancesJour = App\Models\Echeance::with('client')
                            ->whereDate('date_echeance', today())
                            ->where('statut', 'en_attente')
                            ->get();
    @endphp
    
    @if($echeancesJour->count() > 0)
        <div class="space-y-3">
            @foreach($echeancesJour as $echeance)
            <div class="flex items-center justify-between bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="bg-yellow-100 rounded-full p-2">
                        <span class="text-yellow-600">👤</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $echeance->client->prenom }} {{ $echeance->client->nom }}</p>
                        <p class="text-sm text-gray-600">{{ $echeance->client->telephone }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="font-bold text-red-600">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</p>
                        <p class="text-xs text-gray-500">Échéance: {{ $echeance->date_echeance->format('d/m/Y') }}</p>
                    </div>
                    <a href="{{ route('echeances.marquerPayee', $echeance) }}" 
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition duration-300"
                       onclick="return confirm('Marquer ce paiement comme effectué ?')">
                        ✅ Payer
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <div class="text-6xl mb-4">🎉</div>
            <p class="text-gray-600 text-lg">Aucun paiement à recevoir aujourd'hui</p>
            <p class="text-gray-400 text-sm">Vous êtes à jour !</p>
        </div>
    @endif
</div>
@endsection