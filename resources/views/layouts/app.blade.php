<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion Kits Scolaires')</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <a href="{{ route('dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">🏠</a>
                        <a href="{{ route('clients.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">👥 Clients</a>
                        <a href="{{ route('articles.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">📦 Articles</a>
                        <a href="{{ route('kits.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">🎒 Kits</a>
                        <a href="{{ route('ventes.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">🛒 Ventes</a>
                        <a href="{{ route('echeances.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">📅 Échéances</a>
                        <a href="{{ route('stock.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">📊 Stock</a>
                        <a href="{{ route('rapport.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm">📊 Rapport</a>
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