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
Route::get('/clients/export-csv', [ClientController::class, 'exportCSV'])->name('clients.export-csv');
Route::resource('clients', ClientController::class);

// ========== ARTICLES ==========
Route::get('/articles/export-csv', [ArticleController::class, 'exportCSV'])->name('articles.export-csv');
Route::post('/articles/import-csv', [ArticleController::class, 'importCSV'])->name('articles.import-csv');
Route::resource('articles', ArticleController::class);

// ========== KITS ==========
Route::get('/kits/export-csv', [KitController::class, 'exportCSV'])->name('kits.export-csv');
Route::resource('kits', KitController::class);

// ========== VENTES ==========
Route::get('/ventes/export-csv', [VenteController::class, 'exportCSV'])->name('ventes.export-csv');
Route::get('/ventes/{vente}/facture', [VenteController::class, 'facture'])->name('ventes.facture');
Route::get('/ventes/{vente}/facture-preview', [VenteController::class, 'facturePreview'])->name('ventes.facture-preview');
Route::resource('ventes', VenteController::class);

// ========== ÉCHÉANCES ==========
Route::get('/echeances', [EcheanceController::class, 'index'])->name('echeances.index');
Route::get('/echeances/{echeance}/payer', [EcheanceController::class, 'marquerPayee'])->name('echeances.payer');
Route::get('/echeances/{echeance}/retard', [EcheanceController::class, 'marquerRetard'])->name('echeances.retard');

// ========== STOCK ==========
Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
Route::get('/stock/export-csv', [StockController::class, 'exportCSV'])->name('stock.export-csv');
Route::get('/stock/article/{article}', [StockController::class, 'historique'])->name('stock.historique');

// ========== RAPPORT ==========
Route::get('/rapport', [RapportController::class, 'index'])->name('rapport.index');
Route::get('/rapport/pdf', [RapportController::class, 'exportPDF'])->name('rapport.pdf');