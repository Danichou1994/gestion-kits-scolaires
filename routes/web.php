<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\EcheanceController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('clients', ClientController::class);
Route::resource('articles', ArticleController::class);
Route::resource('kits', KitController::class);
Route::resource('ventes', VenteController::class);
Route::resource('echeances', EcheanceController::class);
// Routes pour les actions spéciales des échéances
Route::get('/echeances/{echeance}/marquer-payee', [App\Http\Controllers\EcheanceController::class, 'marquerPayee'])->name('echeances.marquerPayee');
Route::get('/echeances/{echeance}/marquer-retard', [App\Http\Controllers\EcheanceController::class, 'marquerRetard'])->name('echeances.marquerRetard');