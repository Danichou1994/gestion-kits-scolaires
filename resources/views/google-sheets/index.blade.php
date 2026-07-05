@extends('layouts.app')

@section('title', 'Export CSV')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-8">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-4">📊 Export CSV</h1>
        <p class="text-center text-gray-600 mb-8">Exporter toutes vos données en CSV</p>

        <div class="grid grid-cols-1 gap-4">
            <a href="{{ route('google-sheets.export-all') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white text-center py-6 px-4 rounded-xl transition duration-300">
                <div class="text-5xl mb-2">📥</div>
                <div class="font-bold text-lg">Exporter en CSV</div>
                <div class="text-sm opacity-80">Clients, Ventes, Échéances</div>
            </a>
        </div>
    </div>
</div>
@endsection