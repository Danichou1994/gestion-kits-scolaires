<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des clients</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; padding: 20px; }
        h1 { color: #2563EB; text-align: center; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #2563EB; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
        .total { text-align: right; margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>📋 Liste des clients</h1>
    <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    
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
    
    <p class="total">Total : {{ $clients->count() }} clients</p>
    <p class="footer">© Gestion Kits Scolaires - Lomé</p>
</body>
</html>