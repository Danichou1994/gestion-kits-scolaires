@extends('layouts.app')

@section('title', 'Liste des clients')
<div class="flex flex-wrap gap-2 mb-4">
    <a href="{{ route('clients.export-pdf') }}" 
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition duration-300 flex items-center">
        📄 Exporter PDF
    </a>
</div>
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">👥 Liste des clients</h1>
    <a href="{{ route('clients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Ajouter un client
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Nom</th>
                <th class="px-6 py-3 text-left">Prénom</th>
                <th class="px-6 py-3 text-left">Téléphone</th>
                <th class="px-6 py-3 text-left">Quartier</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
            <tr class="border-t">
                <td class="px-6 py-3">{{ $client->nom }}</td>
                <td class="px-6 py-3">{{ $client->prenom }}</td>
                <td class="px-6 py-3">{{ $client->telephone }}</td>
                <td class="px-6 py-3">{{ $client->quartier }}</td>
                <td class="px-6 py-3">
                    <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:underline mr-2">Voir</a>
                    <a href="{{ route('clients.edit', $client) }}" class="text-yellow-600 hover:underline mr-2">Modifier</a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun client enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection