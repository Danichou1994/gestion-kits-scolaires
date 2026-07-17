@extends('layouts.app')

@section('title', 'Gestion des kits')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">🎒 Gestion des kits</h1>
    <a href="{{ route('kits.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Nouveau kit
    </a>
</div>

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

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left">ID</th>
                <th class="px-4 py-3 text-left">Nom</th>
                <th class="px-4 py-3 text-right">Prix total</th>
                <th class="px-4 py-3 text-right">Prix final</th>
                <th class="px-4 py-3 text-center">Articles</th>
                <th class="px-4 py-3 text-center">Promotion</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kits as $kit)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3">{{ $kit->id }}</td>
                <td class="px-4 py-3 font-semibold">{{ $kit->nom_kit }}</td>
                <td class="px-4 py-3 text-right">{{ number_format($kit->prix_total ?? 0) }} F</td>
                <td class="px-4 py-3 text-right font-bold text-blue-600">{{ number_format($kit->prix_final ?? 0) }} F</td>
                <td class="px-4 py-3 text-center">{{ $kit->articles->count() }}</td>
                <td class="px-4 py-3 text-center">
                    @if($kit->en_promotion && $kit->date_debut_promo <= now() && $kit->date_fin_promo >= now())
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✅ Active</span>
                    @elseif($kit->en_promotion)
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">⏳ À venir</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-sm">❌ Non</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('kits.show', $kit) }}" class="text-blue-600 hover:text-blue-800">👁️</a>
                        <a href="{{ route('kits.edit', $kit) }}" class="text-green-600 hover:text-green-800">✏️</a>
                        <form action="{{ route('kits.destroy', $kit) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce kit ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500">Aucun kit enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection