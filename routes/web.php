<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\EcheanceController;
use App\Exports\ClientsExcelExport;
use App\Exports\VentesExcelExport;
use App\Exports\EcheancesExcelExport;
use Barryvdh\DomPDF\Facade\Pdf;

// Page d'accueil
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Ressources CRUD
Route::resource('clients', ClientController::class);
Route::resource('articles', ArticleController::class);
Route::resource('kits', KitController::class);
Route::resource('ventes', VenteController::class);
Route::resource('echeances', EcheanceController::class);

// Actions spéciales pour les échéances
Route::get('/echeances/{echeance}/marquer-payee', [EcheanceController::class, 'marquerPayee'])->name('echeances.marquerPayee');
Route::get('/echeances/{echeance}/marquer-retard', [EcheanceController::class, 'marquerRetard'])->name('echeances.marquerRetard');

// ========== EXPORTS EXCEL ==========
Route::get('/clients/export-excel', function () {
    return ClientsExcelExport::download();
})->name('clients.export-excel');

Route::get('/ventes/export-excel', function () {
    return VentesExcelExport::download();
})->name('ventes.export-excel');

Route::get('/echeances/export-excel', function () {
    return EcheancesExcelExport::download();
})->name('echeances.export-excel');

// ========== EXPORTS PDF ==========
Route::get('/clients/export-pdf', function () {
    $clients = App\Models\Client::all();
    $pdf = Pdf::loadView('pdf.clients', compact('clients'));
    return $pdf->download('clients-' . date('Y-m-d') . '.pdf');
})->name('clients.export-pdf');

Route::get('/ventes/export-pdf', function () {
    $ventes = App\Models\Vente::with(['client', 'kit'])->get();
    $pdf = Pdf::loadView('pdf.ventes', compact('ventes'));
    return $pdf->download('ventes-' . date('Y-m-d') . '.pdf');
})->name('ventes.export-pdf');

Route::get('/echeances/export-pdf', function () {
    $echeances = App\Models\Echeance::with(['client', 'vente'])->get();
    $pdf = Pdf::loadView('pdf.echeances', compact('echeances'));
    return $pdf->download('echeances-' . date('Y-m-d') . '.pdf');
})->name('echeances.export-pdf');