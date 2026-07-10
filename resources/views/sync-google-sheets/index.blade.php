@extends('layouts.app')

@section('title', 'Synchronisation Google Sheets')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-4">📊 Google Sheets</h1>
        <p class="text-center text-gray-600 mb-8">Synchronisez vos données avec Google Sheets</p>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
            <a href="{{ route('sync.sheets.all') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
                <div class="text-5xl mb-2">🔄</div>
                <div class="font-bold text-lg">Tout synchroniser</div>
                <div class="text-sm opacity-80">Toutes les données</div>
            </a>

            <a href="{{ route('sync.sheets.clients') }}" 
               class="bg-green-600 hover:bg-green-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
                <div class="text-5xl mb-2">👥</div>
                <div class="font-bold text-lg">Clients</div>
                <div class="text-sm opacity-80">Synchroniser les clients</div>
            </a>

            <a href="{{ route('sync.sheets.ventes') }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
                <div class="text-5xl mb-2">🛒</div>
                <div class="font-bold text-lg">Ventes</div>
                <div class="text-sm opacity-80">Synchroniser les ventes</div>
            </a>

            <a href="{{ route('sync.sheets.echeances') }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
                <div class="text-5xl mb-2">📅</div>
                <div class="font-bold text-lg">Échéances</div>
                <div class="text-sm opacity-80">Synchroniser les échéances</div>
            </a>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-bold text-gray-700 mb-2">💡 Informations</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>✅ Les données sont exportées depuis votre base de données</li>
                <li>✅ Google Sheets est une copie de sauvegarde en lecture</li>
                <li>✅ Vos données originales restent dans PostgreSQL</li>
            </ul>
        </div>
    </div>
</div>
@endsection