<!-- Actions rapides -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    <a href="{{ route('clients.create') }}" class="bg-blue-500 text-white rounded-lg p-4 text-center hover:bg-blue-600">
        <div class="text-2xl">👤</div>
        <div class="text-sm">Nouveau client</div>
    </a>
    <a href="{{ route('ventes.create') }}" class="bg-green-500 text-white rounded-lg p-4 text-center hover:bg-green-600">
        <div class="text-2xl">🛒</div>
        <div class="text-sm">Nouvelle vente</div>
    </a>
    <a href="{{ route('echeances.index') }}" class="bg-yellow-500 text-white rounded-lg p-4 text-center hover:bg-yellow-600">
        <div class="text-2xl">📅</div>
        <div class="text-sm">Échéances</div>
    </a>
    <a href="{{ route('articles.create') }}" class="bg-purple-500 text-white rounded-lg p-4 text-center hover:bg-purple-600">
        <div class="text-2xl">📦</div>
        <div class="text-sm">Nouvel article</div>
    </a>
</div>