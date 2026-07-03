<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- @include('laravel-pwa::meta') --}}
    <title>@yield('title', 'Gestion Kits Scolaires')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-white font-bold text-xl">📚 Gestion Kits</span>
                    </div>
                    <div class="flex items-center space-x-1 md:space-x-4 overflow-x-auto">
                        <a href="{{ route('dashboard') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                            🏠 Accueil
                        </a>
                        <a href="{{ route('clients.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                            👥 Clients
                        </a>
                        <a href="{{ route('articles.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                            📦 Articles
                        </a>
                        <a href="{{ route('kits.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                            🎒 Kits
                        </a>
                        <a href="{{ route('ventes.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                            🛒 Ventes
                        </a>
                        <a href="{{ route('echeances.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                            📅 Échéances
                        </a>
                        <a href="{{ route('rapport.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                            📊 Rapport
                        </a>
                       <a href="{{ route('google-sheets.index') }}" class="text-white hover:bg-blue-700 px-3 py-2 rounded-lg text-sm transition duration-300 whitespace-nowrap">
                           📊 Google Sheets
                       </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Messages de succès -->
        @if(session('success'))
        <div class="max-w-7xl mx-auto mt-4 px-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Contenu -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>
    {{-- @include('laravel-pwa::register') --}}
</body>
</html>