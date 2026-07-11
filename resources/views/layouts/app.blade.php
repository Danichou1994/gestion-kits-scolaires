<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion Kits Scolaires')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Styles pour le dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 8px;
            z-index: 1000;
            padding: 5px 0;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown-content a {
            color: #333;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: background 0.2s;
        }
        .dropdown-content a:hover {
            background-color: #f1f5f9;
        }
        .dropdown-content a:first-child {
            border-radius: 8px 8px 0 0;
        }
        .dropdown-content a:last-child {
            border-radius: 0 0 8px 8px;
        }
        /* Pour mobile */
        @media (max-width: 768px) {
            .dropdown-content {
                min-width: 160px;
                right: -10px;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-white font-bold text-xl">📚 Gestion Kits</span>
                    </div>
                    <div class="flex items-center space-x-1 md:space-x-2 overflow-x-auto">
                        <a href="{{ route('dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">🏠</a>
                        <a href="{{ route('clients.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">👥 Clients</a>
                        <a href="{{ route('articles.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">📦 Articles</a>
                        <a href="{{ route('kits.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">🎒 Kits</a>
                        <a href="{{ route('ventes.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">🛒 Ventes</a>
                        <a href="{{ route('echeances.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">📅 Échéances</a>
                        <a href="{{ route('stock.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">📊 Stock</a>
                        <a href="{{ route('rapport.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">📊 Rapport</a>
                        <a href="{{ route('sync.sheets.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">📊 Google Sheets</a>
                        
                        <!-- Dropdown Sauvegarde -->
                        <div class="dropdown">
                            <button class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm whitespace-nowrap">
                                💾 Sauvegarde ▼
                            </button>
                            <div class="dropdown-content">
                                <a href="{{ route('backup.pdf') }}">📄 Télécharger PDF</a>
                                <a href="{{ route('backup.download-all') }}">📦 Télécharger tout (ZIP)</a>
                                <a href="{{ route('backup.auto') }}">🔄 Sauvegarder maintenant</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto mt-4 px-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto mt-4 px-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Contenu -->
        <main class="max-w-7xl mx-auto py-6 px-4">
            @yield('content')
        </main>
    </div>
</body>
</html>