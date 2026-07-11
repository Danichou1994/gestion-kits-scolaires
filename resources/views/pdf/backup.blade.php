<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport complet - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; padding: 15px; }
        h1 { color: #2563EB; font-size: 18px; text-align: center; }
        h2 { color: #1e293b; font-size: 14px; margin-top: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #2563EB; color: white; padding: 5px; text-align: left; font-size: 9px; }
        td { padding: 4px; border-bottom: 1px solid #ddd; font-size: 9px; }
        .footer { text-align: center; margin-top: 20px; color: #94a3b8; font-size: 8px; }
        .total { font-weight: bold; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>📊 RAPPORT COMPLET</h1>
    <p style="text-align:center; font-size:11px;">Généré le {{ now()->format('d/m/Y H:i') }}</p>

    <!-- Clients -->
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
            @foreach($clients as $index => $c)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $c->nom }}</td>
                <td>{{ $c->prenom }}</td>
                <td>{{ $c->telephone }}</td>
                <td>{{ $c->quartier ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Articles -->
    <h2>📦 Articles ({{ $articles->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prix achat</th>
                <th>Prix vente</th>
                <th>Bénéfice</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $index => $a)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $a->nom_article }}</td>
                <td>{{ $a->prix_achat }}</td>
                <td>{{ $a->prix_vente }}</td>
                <td>{{ $a->benefice }}</td>
                <td>{{ $a->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Kits -->
    <h2>🎒 Kits ({{ $kits->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prix total</th>
                <th>Prix final</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kits as $index => $k)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $k->nom_kit }}</td>
                <td>{{ $k->prix_total }}</td>
                <td>{{ $k->prix_final }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ventes -->
    <h2>🛒 Ventes ({{ $ventes->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Total</th>
                <th>Acompte</th>
                <th>Solde</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventes as $index => $v)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $v->client->nom }}</td>
                <td>{{ $v->montant_total }}</td>
                <td>{{ $v->acompte }}</td>
                <td>{{ $v->solde }}</td>
                <td>{{ $v->statut }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Échéances -->
    <h2>📅 Échéances ({{ $echeances->count() }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Client</th>
                <th>Montant</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($echeances as $index => $e)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $e->client->nom }}</td>
                <td>{{ $e->montant_dû }}</td>
                <td>{{ $e->date_echeance->format('d/m/Y') }}</td>
                <td>{{ $e->statut }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('etablissement.nom') }} - {{ config('etablissement.adresse') }}</p>
        <p>📞 {{ config('etablissement.telephone') }} | ✉️ {{ config('etablissement.email') }}</p>
    </div>
</body>
</html>