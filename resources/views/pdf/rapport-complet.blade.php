<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport complet</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; padding: 20px; font-size: 12px; }
        h1 { text-align: center; color: #2563EB; font-size: 24px; }
        h2 { color: #1e293b; font-size: 18px; margin-top: 30px; border-bottom: 2px solid #2563EB; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #2563EB; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .footer { text-align: center; margin-top: 30px; color: #94a3b8; font-size: 11px; border-top: 1px solid #ddd; padding-top: 15px; }
        .total { font-weight: bold; margin-top: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>📊 RAPPORT COMPLET</h1>
    <p style="text-align:center; color:#64748b;">Généré le {{ now()->format('d/m/Y à H:i') }}</p>

    <!-- ====== CLIENTS ====== -->
    <h2>👥 Clients ({{ $clients->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Quartier</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $index => $client)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $client->nom }}</td>
                <td>{{ $client->prenom }}</td>
                <td>{{ $client->telephone }}</td>
                <td>{{ $client->quartier ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ====== KITS ====== -->
    <h2>📦 Kits ({{ $kits->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom du kit</th>
                <th>Prix total</th>
                <th>Articles</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kits as $index => $kit)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $kit->nom_kit }}</td>
                <td>{{ number_format($kit->prix_total, 0, ',', ' ') }} F</td>
                <td>
                    @foreach($kit->articles as $article)
                        {{ $article->nom_article }} (x{{ $article->pivot->quantite }})@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ====== ARTICLES ====== -->
    <h2>📦 Articles ({{ $articles->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Catégorie</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $index => $article)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $article->nom_article }}</td>
                <td>{{ number_format($article->prix_unitaire, 0, ',', ' ') }} F</td>
                <td>{{ $article->categorie }}</td>
                <td>{{ $article->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ====== VENTES ====== -->
    <h2>🛒 Ventes ({{ $ventes->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Kit</th>
                <th>Total</th>
                <th>Acompte</th>
                <th>Solde</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventes as $index => $vente)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $vente->client->prenom }} {{ $vente->client->nom }}</td>
                <td>{{ $vente->kit->nom_kit ?? 'N/A' }}</td>
                <td>{{ number_format($vente->montant_total, 0, ',', ' ') }} F</td>
                <td>{{ number_format($vente->acompte, 0, ',', ' ') }} F</td>
                <td>{{ number_format($vente->solde, 0, ',', ' ') }} F</td>
                <td>{{ $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- ====== ÉCHÉANCES ====== -->
    <h2>📅 Échéances ({{ $echeances->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Montant</th>
                <th>Date échéance</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($echeances as $index => $echeance)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $echeance->client->prenom }} {{ $echeance->client->nom }}</td>
                <td>{{ number_format($echeance->montant_dû, 0, ',', ' ') }} F</td>
                <td>{{ $echeance->date_echeance->format('d/m/Y') }}</td>
                <td>{{ $echeance->statut == 'paye' ? 'Payé' : ($echeance->statut == 'en_attente' ? 'En attente' : 'En retard') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        © {{ date('Y') }} Gestion Kits Scolaires - Lomé • Tous droits réservés
    </div>
</body>
</html>