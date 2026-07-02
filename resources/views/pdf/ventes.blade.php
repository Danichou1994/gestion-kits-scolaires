<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des ventes</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; padding: 20px; }
        h1 { color: #16A34A; text-align: center; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #16A34A; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        .total { text-align: right; margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🛒 Liste des ventes</h1>
    <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Kit</th>
                <th>Total</th>
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
                <td>{{ number_format($vente->solde, 0, ',', ' ') }} F</td>
                <td>{{ $vente->statut == 'en_cours' ? 'En cours' : ($vente->statut == 'termine' ? 'Terminé' : 'Annulé') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <p class="total">Total : {{ $ventes->count() }} ventes</p>
    <p class="footer">© Gestion Kits Scolaires - Lomé</p>
</body>
</html>