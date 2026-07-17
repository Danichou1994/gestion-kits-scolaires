@extends('layouts.app')

@section('title', 'Gestion des échéances')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">📅 Gestion des échéances</h1>
    <div class="flex gap-2">
        <a href="{{ route('echeances.export-csv') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            📥 Export CSV
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-blue-100 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $totalEcheances }}</div>
        <div class="text-sm text-gray-600">Total</div>
    </div>
    <div class="bg-green-100 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-green-600">{{ $totalPayees }}</div>
        <div class="text-sm text-gray-600">Payées</div>
    </div>
    <div class="bg-yellow-100 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-yellow-600">{{ $totalAttente }}</div>
        <div class="text-sm text-gray-600">En attente</div>
    </div>
    <div class="bg-red-100 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-red-600">{{ $totalRetard }}</div>
        <div class="text-sm text-gray-600">En retard</div>
    </div>
    <div class="bg-purple-100 p-4 rounded-lg text-center">
        <div class="text-2xl font-bold text-purple-600">{{ number_format($montantTotalDu, 0, ',', ' ') }} F</div>
        <div class="text-sm text-gray-600">Montant dû</div>
    </div>
</div>

<!-- Échéances du jour -->
@if($echeancesAujourdhui->count() > 0)
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <h3 class="font-bold text-yellow-800 mb-3">🔔 Échéances du jour ({{ $echeancesAujourdhui->count() }})</h3>
    <div class="space-y-2">
        @foreach($echeancesAujourdhui as $echeance)
        <div class="flex justify-between items-center bg-white p-3 rounded shadow-sm">
            <div>
                <span class="font-semibold">{{ $echeance->client->prenom }} {{ $echeance->client->nom }}</span>
                <span class="text-sm text-gray-600 ml-2">Vente #{{ $echeance->vente->numero_vente ?? 'N/A' }}</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="font-bold text-red-600">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</span>
                <a href="{{ route('echeances.payer', $echeance) }}" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                    ✅ Payer
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Vue par vente -->
<h2 class="text-xl font-bold mb-4">📋 Échéances par vente</h2>

<div class="space-y-4">
    @forelse($ventesAvecEcheances as $vente)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- En-tête de la vente -->
        <div class="bg-gray-50 px-4 py-3 border-b flex flex-wrap justify-between items-center cursor-pointer" onclick="toggleVente('vente-{{ $vente->id }}')">
            <div class="flex items-center gap-4">
                <span class="font-bold text-blue-600">{{ $vente->numero_vente }}</span>
                <span class="text-gray-700">{{ $vente->client->prenom }} {{ $vente->client->nom }}</span>
                <span class="text-sm text-gray-500">📞 {{ $vente->client->telephone }}</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm font-semibold">Total: {{ number_format($vente->montant_total, 0, ',', ' ') }} F</span>
                <span class="text-sm font-semibold text-red-600">Solde: {{ number_format($vente->solde, 0, ',', ' ') }} F</span>
                <span class="text-sm">
                    @if($vente->statut == 'termine')
                        <span class="text-green-600">✅ Terminé</span>
                    @else
                        <span class="text-yellow-600">⏳ En cours</span>
                    @endif
                </span>
                <span class="text-gray-400 text-sm">▼</span>
            </div>
        </div>

        <!-- Détails des échéances -->
        <div id="vente-{{ $vente->id }}" class="hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Date d'échéance</th>
                        <th class="px-4 py-2 text-right">Montant</th>
                        <th class="px-4 py-2 text-center">Statut</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vente->echeances as $index => $echeance)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">
                            {{ $echeance->date_echeance->format('d/m/Y') }}
                            @if($echeance->date_echeance->isToday() && $echeance->statut != \App\Models\Echeance::STATUT_PAYE)
                                <span class="bg-yellow-200 text-yellow-800 px-1 py-0.5 rounded text-xs">Aujourd'hui</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right font-semibold">{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                        <td class="px-4 py-2 text-center">
                            @if($echeance->statut == \App\Models\Echeance::STATUT_PAYE)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">✅ Payé</span>
                            @elseif($echeance->statut == \App\Models\Echeance::STATUT_EN_RETARD)
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">⚠️ Retard</span>
                            @else
                                @if($echeance->date_echeance->isPast())
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">⚠️ En retard</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">⏳ En attente</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-2 text-center">
                            @if($echeance->statut != \App\Models\Echeance::STATUT_PAYE)
                                <a href="{{ route('echeances.payer', $echeance) }}" class="text-green-600 hover:text-green-800 text-sm mr-2">✅ Payer</a>
                                @if($echeance->statut != \App\Models\Echeance::STATUT_EN_RETARD)
                                    <a href="{{ route('echeances.retard', $echeance) }}" class="text-red-600 hover:text-red-800 text-sm mr-2">⚠️ Retard</a>
                                @endif
                                <a href="{{ route('echeances.attente', $echeance) }}" class="text-yellow-600 hover:text-yellow-800 text-sm">↩️</a>
                            @else
                                <span class="text-green-600 text-sm">✅ Payé le {{ $echeance->date_paiement ? $echeance->date_paiement->format('d/m/Y') : '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Informations supplémentaires sur le client -->
            <div class="bg-gray-50 px-4 py-2 border-t flex gap-6 text-sm">
                <span>📞 Client: {{ $vente->client->telephone }}</span>
                @if($vente->client->adresse)
                    <span>📍 {{ $vente->client->adresse }}</span>
                @endif
                @if($vente->client->quartier)
                    <span>🏘️ {{ $vente->client->quartier }}</span>
                @endif
                <a href="{{ route('clients.show', $vente->client) }}" class="text-blue-600 hover:underline">👤 Voir le client</a>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
        Aucune échéance enregistrée
    </div>
    @endforelse
</div>

<!-- Script pour toggle -->
<script>
    function toggleVente(id) {
        const element = document.getElementById(id);
        if (element.classList.contains('hidden')) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    }
</script>
@endsection