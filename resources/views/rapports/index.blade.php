@extends('layouts.app')

@section('title', 'Rapport complet')

@section('content')
<div class="bg-white rounded-lg shadow p-8 max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-center text-blue-600 mb-4">📊 Rapport complet</h1>
    <p class="text-center text-gray-600 mb-8">Téléchargez un rapport complet avec toutes vos données</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- PDF -->
        <a href="{{ route('rapport.pdf') }}" 
           class="bg-red-600 hover:bg-red-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
            <div class="text-5xl mb-2">📄</div>
            <div class="font-bold text-lg">PDF</div>
            <div class="text-sm opacity-80">Rapport complet</div>
        </a>
    </div>

    <div class="mt-6 text-center text-gray-500 text-sm">
        <p>Le rapport contient :</p>
        <ul class="mt-2 space-y-1">
            <li>👥 Clients</li>
            <li>📦 Kits et Articles</li>
            <li>🛒 Ventes</li>
            <li>📅 Échéances</li>
        </ul>
    </div>
</div>
@endsection