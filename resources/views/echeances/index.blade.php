@extends('layouts.app')

@section('title', 'Gestion des échéances')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📅 Gestion des échéances</h1>
    <a href="{{ route('echeances.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
        + Ajouter une échéance
    </a>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-green-100 rounded-lg p-4">
        <div class="text-2xl font-bold text-green-800">{{ $echeancesPayees }}</div>
        <div class="text-green-600">Payées</div>
    </div>
    <div class="bg-yellow-100 rounded-lg p-4">
        <div class="text-2xl font-bold text-yellow-800">{{ $echeancesAJour }}</div>
        <div class="text-yellow-600">À jour</div>
    </div>
    <div class="bg-red-100 rounded-lg p-4">
        <div class="text-2xl font-bold text-red-800">{{ $echeancesRetard }}</div>
        <div class="text-red-600">En retard</div>
    </div>
</div>

<!-- Liste des échéances -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Client</th>
                <th class="px-6 py-3 text-left">Montant</th>
                <th class="px-6 py-3 text-left">Date d'échéance</th>
                <th class="px-6 py-3 text-left">Statut</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($echeances as $echeance)
            <tr class="border-t">
                <td class="px-6 py-3">{{ $echeance->client->prenom }} {{ $echeance->client->nom }}</td>
                <td class="px-6 py-3">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                <td class="px-6 py-3">
                    {{ $echeance->date_echeance->format('d/m/Y') }}
                    @if($echeance->date_echeance < today() && $echeance->statut != 'paye')
                        <span class="text-red-600 ml-2">⚠️</span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    @if($echeance->statut == 'en_attente')
                        @if($echeance->date_echeance < today())
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">⚠️ En retard</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">En attente</span>
                        @endif
                    @elseif($echeance->statut == 'paye')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✅ Payé</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">En retard</span>
                    @endif
                </td>
                <td class="px-6 py-3">
                    <a href="{{ route('echeances.show', $echeance) }}" class="text-blue-600 hover:underline mr-2">Voir</a>
                    
                    @if($echeance->statut != 'paye')
                        <a href="{{ route('echeances.marquerPayee', $echeance) }}" 
                           class="text-green-600 hover:underline mr-2"
                           onclick="return confirm('Marquer cette échéance comme payée ?')">
                            ✅ Payer
                        </a>
                        @if($echeance->date_echeance < today())
                            <a href="{{ route('echeances.marquerRetard', $echeance) }}" 
                               class="text-red-600 hover:underline mr-2"
                               onclick="return confirm('Marquer cette échéance comme en retard ?')">
                                ⚠️ Retard
                            </a>
                        @endif
                    @endif
                    
                    <a href="{{ route('echeances.edit', $echeance) }}" class="text-yellow-600 hover:underline mr-2">Modifier</a>
                    
                    <form action="{{ route('echeances.destroy', $echeance) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette échéance ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune échéance enregistrée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection