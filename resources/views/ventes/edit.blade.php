@extends('layouts.app')

@section('title', 'Modifier la vente')

@section('content')
<h1 class="text-2xl font-bold mb-6">✏️ Modifier la vente</h1>

<div class="bg-white rounded-lg shadow p-6 max-w-4xl">
    <form action="{{ route('ventes.update', $vente) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 mb-2">Client</label>
                <select name="client_id" class="w-full border rounded-lg px-3 py-2" required>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ $vente->client_id == $client->id ? 'selected' : '' }}>
                        {{ $client->prenom }} {{ $client->nom }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Mode paiement</label>
                <select name="mode_paiement" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Sélectionner</option>
                    <option value="especes" {{ $vente->mode_paiement == 'especes' ? 'selected' : '' }}>Espèces</option>
                    <option value="mobile_money" {{ $vente->mode_paiement == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="cheque" {{ $vente->mode_paiement == 'cheque' ? 'selected' : '' }}>Chèque</option>
                    <option value="virement" {{ $vente->mode_paiement == 'virement' ? 'selected' : '' }}>Virement</option>
                    <option value="carte" {{ $vente->mode_paiement == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 mb-2">Mensualités</label>
                <select name="nb_mensualites" class="w-full border rounded-lg px-3 py-2">
                    <option value="2" {{ $vente->nb_mensualites == 2 ? 'selected' : '' }}>2</option>
                    <option value="3" {{ $vente->nb_mensualites == 3 ? 'selected' : '' }}>3</option>
                    <option value="4" {{ $vente->nb_mensualites == 4 ? 'selected' : '' }}>4</option>
                    <option value="6" {{ $vente->nb_mensualites == 6 ? 'selected' : '' }}>6</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Date vente</label>
                <input type="date" name="date_vente" class="w-full border rounded-lg px-3 py-2" value="{{ $vente->date_vente->format('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Statut</label>
                <select name="statut" class="w-full border rounded-lg px-3 py-2">
                    <option value="en_cours" {{ $vente->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="termine" {{ $vente->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                    <option value="annule" {{ $vente->statut == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="block text-gray-700 mb-2">Remise (FCFA)</label>
                <input type="number" name="remise" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="{{ $vente->remise ?? 0 }}">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais livraison</label>
                <input type="number" name="frais_livraison" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="{{ $vente->frais_livraison ?? 0 }}">
            </div>
            <div>
                <label class="block text-gray-700 mb-2">Frais carnet</label>
                <input type="number" name="frais_carnet" class="w-full border rounded-lg px-3 py-2" min="0" step="0.01" value="{{ $vente->frais_carnet ?? 0 }}">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-gray-700 mb-2">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2" rows="2">{{ $vente->notes }}</textarea>
        </div>

        <div class="mt-6">
            <h3 class="font-bold text-lg mb-2">💰 Récapitulatif</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-lg">
                <div>
                    <span class="text-gray-600">Total</span>
                    <div class="text-xl font-bold text-blue-600">{{ number_format($vente->montant_total, 0, ',', ' ') }} F</div>
                </div>
                <div>
                    <span class="text-gray-600">Acompte (1/4)</span>
                    <div class="text-xl font-bold text-green-600">{{ number_format($vente->acompte, 0, ',', ' ') }} F</div>
                </div>
                <div>
                    <span class="text-gray-600">Solde</span>
                    <div class="text-xl font-bold text-red-600">{{ number_format($vente->solde, 0, ',', ' ') }} F</div>
                </div>
                <div>
                    <span class="text-gray-600">Mensualités</span>
                    <div class="text-xl font-bold">{{ $vente->nb_mensualites }} x {{ number_format($vente->montant_mensualite, 0, ',', ' ') }} F</div>
                </div>
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
                Mettre à jour
            </button>
            <a href="{{ route('ventes.index') }}" class="text-gray-600 hover:underline">Annuler</a>
        </div>
    </form>
</div>
@endsection