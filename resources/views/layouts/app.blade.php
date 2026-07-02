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
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex space-x-8">
                        <a href="{{ route('dashboard') }}" class="flex items-center text-gray-700 hover:text-blue-600">
                            🏠 Accueil
                        </a>
                        <a href="{{ route('clients.index') }}" class="flex items-center text-gray-700 hover:text-blue-600">
                            👥 Clients
                        </a>
                        <a href="{{ route('articles.index') }}" class="flex items-center text-gray-700 hover:text-blue-600">
                            📦 Articles
                        </a>
                        <a href="{{ route('kits.index') }}" class="flex items-center text-gray-700 hover:text-blue-600">
                            📦 Kits
                        </a>
                        <a href="{{ route('ventes.index') }}" class="flex items-center text-gray-700 hover:text-blue-600">
                            🛒 Ventes
                        </a>
                        <a href="{{ route('echeances.index') }}" class="flex items-center text-gray-700 hover:text-blue-600">
                            📅 Échéances
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