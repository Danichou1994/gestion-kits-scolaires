<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des échéances</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; padding: 20px; }
        h1 { color: #DC2626; text-align: center; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #DC2626; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        .total { text-align: right; margin-top: 20px; font-weight: bold; }
        .statut-paye { color: #16a34a; font-weight: bold; }
        .statut-attente { color: #d97706; font-weight: bold; }
        .statut-retard { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <h1>📅 Liste des échéances</h1>
    <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    
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
                <td>
                    @if($echeance->statut == 'paye')
                        <span class="statut-paye">✅ Payé</span>
                    @elseif($echeance->statut == 'en_attente')
                        <span class="statut-attente">⏳ En attente</span>
                    @else
                        <span class="statut-retard">⚠️ En retard</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <p class="total">Total : {{ $echeances->count() }} échéances</p>
    <p class="footer">© Gestion Kits Scolaires - Lomé</p>
</body>
</html>