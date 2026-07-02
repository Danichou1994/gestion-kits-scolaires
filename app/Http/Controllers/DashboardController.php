<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use App\Models\Echeance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $totalVentes = Vente::count();
        $chiffreAffaires = Vente::sum('montant_total');
        
        $echeancesAujourdhui = Echeance::whereDate('date_echeance', today())
                                        ->where('statut', 'en_attente')
                                        ->count();

        return view('dashboard', compact(
            'totalClients', 
            'totalVentes', 
            'chiffreAffaires',
            'echeancesAujourdhui'
        ));
    }
}