<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\EcheanceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\RapportController;
use Barryvdh\DomPDF\Facade\Pdf;

// Page d'accueil
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ========== CLIENTS ==========
Route::resource('clients', ClientController::class);
Route::get('/clients/export-csv', [ClientController::class, 'exportCSV'])->name('clients.export-csv');
Route::get('/clients/search', [ClientController::class, 'search'])->name('clients.search');

// ========== ARTICLES ==========
Route::resource('articles', ArticleController::class);
Route::get('/articles/export-csv', [ArticleController::class, 'exportCSV'])->name('articles.export-csv');
Route::post('/articles/import-csv', [ArticleController::class, 'importCSV'])->name('articles.import-csv');

// ========== KITS ==========
Route::resource('kits', KitController::class);
Route::get('/kits/export-csv', [KitController::class, 'exportCSV'])->name('kits.export-csv');

// ========== VENTES ==========
Route::resource('ventes', VenteController::class);
Route::get('/ventes/{vente}/facture', [VenteController::class, 'facture'])->name('ventes.facture');
Route::get('/ventes/export-csv', [VenteController::class, 'exportCSV'])->name('ventes.export-csv');

// ========== ÉCHÉANCES ==========
Route::get('/echeances', [EcheanceController::class, 'index'])->name('echeances.index');
Route::get('/echeances/vente/{vente}', [EcheanceController::class, 'show'])->name('echeances.show');
Route::get('/echeances/{echeance}/payer', [EcheanceController::class, 'marquerPayee'])->name('echeances.payer');
Route::get('/echeances/{echeance}/retard', [EcheanceController::class, 'marquerRetard'])->name('echeances.retard');

// ========== STOCK ==========
Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
Route::get('/stock/export-csv', [StockController::class, 'exportCSV'])->name('stock.export-csv');
Route::get('/stock/article/{article}', [StockController::class, 'historique'])->name('stock.historique');

// ========== RAPPORT COMPLET ==========
Route::get('/rapport', [RapportController::class, 'index'])->name('rapport.index');
Route::get('/rapport/pdf', [RapportController::class, 'exportPDF'])->name('rapport.pdf');

// ========== TEST ==========
Route::get('/test-routes', function () {
    $routes = [];
    foreach (Route::getRoutes() as $route) {
        $routes[] = $route->uri();
    }
    return response()->json($routes);
});