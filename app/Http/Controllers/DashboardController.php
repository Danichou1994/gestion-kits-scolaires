<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use App\Models\Echeance;
use App\Models\Article;
use App\Models\Kit;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $totalVentes = Vente::count();
        $chiffreAffaires = Vente::sum('montant_total');
        
        // === AJOUTÉ POUR LA COMMISSION ===
        $totalCommission = Vente::sum('commission');
        $totalNet = Vente::sum('montant_net');
        $commissionMois = Vente::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission');
        
        // === BÉNÉFICE TOTAL CORRIGÉ ===
        // Avant : Article::sum('benefice') → ❌ additionne juste les bénéfices unitaires
        // Après : Article::sum(DB::raw('stock * benefice')) → ✅ stock × bénéfice unitaire
        $beneficeTotal = Article::sum(DB::raw('stock * benefice'));
        
        $valeurStock = Article::sum(DB::raw('stock * prix_achat'));
        
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
            'totalCommission', 'totalNet', 'commissionMois',
            'beneficeTotal', 'valeurStock', 'echeancesAujourdhui',
            'echeancesRetard', 'articlesAlerte', 'ventesMois', 'topArticles'
        ));
    }
}