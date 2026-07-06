@extends('layouts.app')

@section('title', 'Échéances')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📅 Échéances</h1>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-green-100 p-4 rounded-lg">
        <span class="text-sm">Payées</span>
        <div class="text-2xl font-bold text-green-600">{{ $echeancesPayees ?? 0 }}</div>
    </div>
    <div class="bg-yellow-100 p-4 rounded-lg">
        <span class="text-sm">En attente</span>
        <div class="text-2xl font-bold text-yellow-600">{{ $echeancesAJour ?? 0 }}</div>
    </div>
    <div class="bg-red-100 p-4 rounded-lg">
        <span class="text-sm">En retard</span>
        <div class="text-2xl font-bold text-red-600">{{ $echeancesRetard ?? 0 }}</div>
    </div>
</div>

<!-- Ventes avec échéances -->
@foreach($ventesAvecEcheances as $vente)
<div class="bg-white rounded-lg shadow mb-4 overflow-hidden">
    <div class="bg-gray-50 px-4 py-2 border-b flex justify-between">
        <span class="font-bold">Vente #{{ $vente->numero_vente }}</span>
        <span>{{ $vente->client->prenom }} {{ $vente->client->nom }}</span>
        <span class="text-sm text-gray-600">Total: {{ number_format($vente->montant_total, 0, ',', ' ') }} F</span>
    </div>
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left">Date</th>
                <th class="px-4 py-2 text-left">Montant</th>
                <th class="px-4 py-2 text-left">Statut</th>
                <th class="px-4 py-2 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->echeances as $echeance)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                <td class="px-4 py-2">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                <td class="px-4 py-2">
                    @if($echeance->statut == 'paye')
                        <span class="text-green-600">✅ Payé</span>
                    @elseif($echeance->statut == 'en_attente')
                        <span class="text-yellow-600">⏳ En attente</span>
                    @else
                        <span class="text-red-600">⚠️ En retard</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    @if($echeance->statut != 'paye')
                        <a href="{{ route('echeances.payer', $echeance) }}" class="text-green-600 hover:underline text-sm">Payer</a>
                        @if($echeance->date_echeance < now())
                            <a href="{{ route('echeances.retard', $echeance) }}" class="text-red-600 hover:underline text-sm ml-2">Retard</a>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach
@endsection