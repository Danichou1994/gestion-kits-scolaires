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
        // ========== STATISTIQUES GÉNÉRALES ==========
        $totalClients = Client::count();
        $totalVentes = Vente::count();
        $chiffreAffaires = Vente::sum('montant_total');
        
        // ========== COMMISSIONS ==========
        $totalCommission = Vente::sum('commission');
        $totalNet = Vente::sum('montant_net');
        $commissionMois = Vente::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission');
        
        // ========== BÉNÉFICE TOTAL CORRIGÉ ==========
        // Méthode 1 : Bénéfice basé sur le stock (stock × bénéfice unitaire)
        $beneficeTotalStock = Article::sum(DB::raw('stock * benefice'));
        
        // Méthode 2 : Bénéfice basé sur les ventes réellement réalisées (PLUS PRÉCIS)
        $beneficeTotalVentes = 0;
        $ventes = Vente::all();
        foreach ($ventes as $vente) {
            $items = json_decode($vente->items, true) ?? [];
            foreach ($items as $item) {
                if ($item['type'] == 'article') {
                    $article = Article::find($item['id']);
                    if ($article) {
                        $beneficeTotalVentes += ($article->prix_vente - $article->prix_achat) * $item['quantite'];
                    }
                } elseif ($item['type'] == 'kit') {
                    $kit = Kit::with('articles')->find($item['id']);
                    if ($kit) {
                        foreach ($kit->articles as $article) {
                            $beneficeTotalVentes += ($article->prix_vente - $article->prix_achat) * $item['quantite'] * $article->pivot->quantite;
                        }
                    }
                }
            }
        }
        
        // Utiliser le bénéfice basé sur les ventes si > 0, sinon utiliser le stock
        $beneficeTotal = $beneficeTotalVentes > 0 ? $beneficeTotalVentes : $beneficeTotalStock;
        
        // ========== VALEUR DU STOCK ==========
        $valeurStock = Article::sum(DB::raw('stock * prix_achat'));
        
        // ========== ÉCHÉANCES ==========
        $echeancesAujourdhui = Echeance::whereDate('date_echeance', today())
                                        ->where('statut', 'en_attente')
                                        ->count();
        
        $echeancesRetard = Echeance::whereDate('date_echeance', '<', today())
                                    ->where('statut', 'en_attente')
                                    ->count();
        
        // ========== ARTICLES EN ALERTE ==========
        $articlesAlerte = Article::whereColumn('stock', '<=', 'seuil_alerte')->count();
        
        // ========== VENTES DU MOIS ==========
        $ventesMois = Vente::whereMonth('date_vente', now()->month)
            ->whereYear('date_vente', now()->year)
            ->count();
        
        // ========== TOP 5 ARTICLES ==========
        $topArticles = Article::orderBy('benefice', 'desc')->limit(5)->get();

        // ========== RETOUR VUE ==========
        return view('dashboard', compact(
            'totalClients',
            'totalVentes',
            'chiffreAffaires',
            'totalCommission',
            'totalNet',
            'commissionMois',
            'beneficeTotal',
            'valeurStock',
            'echeancesAujourdhui',
            'echeancesRetard',
            'articlesAlerte',
            'ventesMois',
            'topArticles'
        ));
    }
}