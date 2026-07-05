<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use App\Models\Echeance;
use App\Models\Article;
use App\Models\Kit;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $totalVentes = Vente::count();
        $chiffreAffaires = Vente::sum('montant_total');
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        
        $echeancesAujourdhui = Echeance::whereDate('date_echeance', today())
                                        ->where('statut', 'en_attente')
                                        ->count();
        
        $echeancesRetard = Echeance::whereDate('date_echeance', '<', today())
                                    ->where('statut', 'en_attente')
                                    ->count();
        
        $articlesAlerte = Article::whereColumn('stock', '<=', 'seuil_alerte')->count();
        
        $ventesMois = Vente::whereMonth('date_vente', now()->month)->count();
        
        $topArticles = Article::orderBy('benefice', 'desc')->limit(5)->get();

        return view('dashboard', compact(
            'totalClients', 'totalVentes', 'chiffreAffaires',
            'beneficeTotal', 'valeurStock', 'echeancesAujourdhui',
            'echeancesRetard', 'articlesAlerte', 'ventesMois', 'topArticles'
        ));
    }
}