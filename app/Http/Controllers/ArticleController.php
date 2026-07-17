<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::orderBy('active', 'desc')->get();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function actifs()
    {
        $articles = Article::where('active', true)->get();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function inactifs()
    {
        $articles = Article::where('active', false)->get();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function toggleActif(Article $article)
    {
        $article->active = !$article->active;
        $article->save();
        $statut = $article->active ? 'activé' : 'désactivé';
        return redirect()->route('articles.index')->with('success', "Article {$statut} avec succès !");
    }

    public function create()
    {
        $categories = [
            'Rangement et organisation' => 'Rangement et organisation',
            'Géométrie et travaux manuels' => 'Géométrie et travaux manuels',
            'Coloriage et surlignage' => 'Coloriage et surlignage',
            'Écriture et correction' => 'Écriture et correction',
            'Lecture et apprentissage' => 'Lecture et apprentissage'
        ];
        
        $unites = [
            'pièce',
            'kg',
            'litre',
            'mètre',
            'boîte',
            'paquet',
            'carton',
            'rame'
        ];
        
        return view('articles.create', compact('categories', 'unites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_article' => 'required|unique:articles|max:255',
            'code_barre' => 'nullable|unique:articles|max:100',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categorie' => 'required|string|max:255',
            'unite_mesure' => 'required|string|max:50',
            'fournisseur' => 'nullable|string|max:255',
            'seuil_alerte' => 'nullable|integer|min:0',
        ]);

        $benefice = $request->prix_vente - $request->prix_achat;

        Article::create([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'prix_unitaire' => $request->prix_vente,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'benefice' => $benefice,
            'categorie' => $request->categorie,
            'fournisseur' => $request->fournisseur,
            'unite_mesure' => $request->unite_mesure,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
            'active' => true,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article ajouté avec succès !');
    }

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $categories = [
            'Rangement et organisation' => 'Rangement et organisation',
            'Géométrie et travaux manuels' => 'Géométrie et travaux manuels',
            'Coloriage et surlignage' => 'Coloriage et surlignage',
            'Écriture et correction' => 'Écriture et correction',
            'Lecture et apprentissage' => 'Lecture et apprentissage'
        ];
        
        $unites = [
            'pièce',
            'kg',
            'litre',
            'mètre',
            'boîte',
            'paquet',
            'carton',
            'rame'
        ];
        
        return view('articles.edit', compact('article', 'categories', 'unites'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom_article' => 'required|unique:articles,nom_article,' . $article->id . '|max:255',
            'code_barre' => 'nullable|unique:articles,code_barre,' . $article->id . '|max:100',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'categorie' => 'required|string|max:255',
            'unite_mesure' => 'required|string|max:50',
            'fournisseur' => 'nullable|string|max:255',
            'seuil_alerte' => 'nullable|integer|min:0',
        ]);

        $benefice = $request->prix_vente - $request->prix_achat;

        $article->update([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'prix_unitaire' => $request->prix_vente,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'benefice' => $benefice,
            'categorie' => $request->categorie,
            'fournisseur' => $request->fournisseur,
            'unite_mesure' => $request->unite_mesure,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article modifié avec succès !');
    }

    public function destroy(Article $article)
    {
        $ventes = \App\Models\Vente::where('items', 'like', '%"id":' . $article->id . ',"type":"article"%')->count();
        
        if ($ventes > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article est utilisé dans ' . $ventes . ' vente(s) et ne peut pas être supprimé.');
        }
        
        $kits = \App\Models\Kit::whereHas('articles', function($query) use ($article) {
            $query->where('article_id', $article->id);
        })->count();
        
        if ($kits > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article est utilisé dans ' . $kits . ' kit(s) et ne peut pas être supprimé.');
        }
        
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succès !');
    }

    public function exportCSV()
    {
        $articles = Article::all();
        $filename = storage_path('app/temp/articles.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, ['ID', 'Nom', 'Code barre', 'Prix achat', 'Prix vente', 'Bénéfice', 'Catégorie', 'Fournisseur', 'Unité', 'Stock', 'Seuil alerte', 'Actif']);

        foreach ($articles as $article) {
            fputcsv($file, [
                $article->id,
                $article->nom_article,
                $article->code_barre,
                $article->prix_achat,
                $article->prix_vente,
                $article->benefice,
                $article->categorie,
                $article->fournisseur,
                $article->unite_mesure ?? 'pièce',
                $article->stock,
                $article->seuil_alerte,
                $article->active ? 'Oui' : 'Non'
            ]);
        }
        fclose($file);

        return response()->download($filename, 'articles-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('fichier');
        $handle = fopen($file, 'r');
        
        $header = fgetcsv($handle);
        
        $header = array_map('trim', $header);
        $header = array_map(function($item) {
            return str_replace("\xEF\xBB\xBF", '', $item);
        }, $header);
        
        $expectedHeader = ['Nom', 'Code barre', 'Prix achat', 'Prix vente', 'Bénéfice', 'Catégorie', 'Fournisseur', 'Unité', 'Stock', 'Seuil alerte'];
        
        if ($header !== $expectedHeader) {
            fclose($handle);
            $erreurMessage = '❌ Le fichier CSV ne correspond pas au format attendu.<br><br>';
            $erreurMessage .= 'En-tête requis :<br>';
            $erreurMessage .= '<code>Nom, Code barre, Prix achat, Prix vente, Bénéfice, Catégorie, Fournisseur, Unité, Stock, Seuil alerte</code><br><br>';
            $erreurMessage .= 'En-tête trouvé :<br>';
            $erreurMessage .= '<code>' . implode(', ', $header) . '</code>';
            
            return redirect()->route('articles.index')
                ->with('error', $erreurMessage);
        }
        
        $compteur = 0;
        $erreurs = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $row = array_map('trim', $row);
                
                if (count($header) !== count($row)) {
                    if (count($row) < count($header)) {
                        $row = array_pad($row, count($header), '');
                    }
                    if (count($row) > count($header)) {
                        $row = array_slice($row, 0, count($header));
                    }
                }
                
                $data = array_combine($header, $row);
                
                if (empty($data['Nom']) || empty($data['Prix achat']) || empty($data['Prix vente'])) {
                    $erreurs[] = "Ligne " . ($compteur + 2) . ": Champs obligatoires manquants";
                    $compteur++;
                    continue;
                }

                $prixAchat = floatval(str_replace([' ', ','], '', $data['Prix achat']));
                $prixVente = floatval(str_replace([' ', ','], '', $data['Prix vente']));
                $stock = intval($data['Stock'] ?? 0);
                $benefice = $prixVente - $prixAchat;

                Article::create([
                    'nom_article' => $data['Nom'],
                    'code_barre' => $data['Code barre'] ?? null,
                    'prix_unitaire' => $prixVente,
                    'prix_achat' => $prixAchat,
                    'prix_vente' => $prixVente,
                    'benefice' => $benefice,
                    'categorie' => $data['Catégorie'] ?? 'Non classé',
                    'fournisseur' => $data['Fournisseur'] ?? null,
                    'unite_mesure' => $data['Unité'] ?? 'pièce',
                    'stock' => $stock,
                    'seuil_alerte' => intval($data['Seuil alerte'] ?? 5),
                    'active' => true,
                ]);
                
                $compteur++;
                
            } catch (\Exception $e) {
                $erreurs[] = "Ligne " . ($compteur + 2) . ": " . $e->getMessage();
                $compteur++;
            }
        }
        
        fclose($handle);

        if (count($erreurs) > 0) {
            return redirect()->route('articles.index')
                ->with('warning', $compteur . ' articles importés, mais ' . count($erreurs) . ' erreurs rencontrées.')
                ->with('erreurs', $erreurs);
        }

        return redirect()->route('articles.index')
            ->with('success', $compteur . ' articles importés avec succès !');
    }
}