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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('google-sheets.export-all') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
                <div class="text-5xl mb-2">🔄</div>
                <div class="font-bold text-lg">Tout synchroniser</div>
                <div class="text-sm opacity-80">Clients, Ventes, Kits, Échéances</div>
            </a>

            <a href="https://docs.google.com/spreadsheets/d/{{ env('GOOGLE_SHEETS_SPREADSHEET_ID') }}" 
               target="_blank" 
               class="bg-purple-600 hover:bg-purple-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
                <div class="text-5xl mb-2">📊</div>
                <div class="font-bold text-lg">Ouvrir Sheets</div>
                <div class="text-sm opacity-80">Voir dans Google Sheets</div>
            </a>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-bold text-gray-700 mb-2">💡 Informations</h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>✅ Les données sont exportées depuis PostgreSQL</li>
                <li>✅ Google Sheets est une copie de sauvegarde</li>
                <li>✅ Vos données originales restent dans PostgreSQL</li>
            </ul>
        </div>
    </div>
</div>
@endsection