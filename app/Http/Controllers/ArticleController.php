<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Stock;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        $beneficeTotal = Article::sum('benefice');
        $valeurStock = Article::sum(\DB::raw('stock * prix_achat'));
        $nbArticles = Article::count();
        $stockTotal = Article::sum('stock');
        return view('articles.index', compact('articles', 'beneficeTotal', 'valeurStock', 'nbArticles', 'stockTotal'));
    }

    public function create()
    {
        $categories = [
            'Rangement et organisation' => '📦 Pour le rangement et l\'organisation',
            'Géométrie et travaux manuels' => '📐 Pour la géométrie et les travaux manuels',
            'Coloriage et surlignage' => '🎨 Pour colorier et surligner',
            'Écriture et correction' => '✍️ Pour écrire et corriger',
            'Lecture et apprentissage' => '📚 Pour lire et apprendre'
        ];
        $unites = ['pièce', 'kg', 'g', 'mètre', 'cm', 'lot', 'boîte', 'paquet'];
        return view('articles.create', compact('categories', 'unites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_article' => 'required|string|max:255|unique:articles',
            'code_barre' => 'nullable|string|max:50|unique:articles',
            'categorie' => 'required|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
            'unite_mesure' => 'required|string',
            'fournisseur' => 'nullable|string|max:255',
            'emplacement' => 'nullable|string|max:100',
        ]);

        // Créer l'article sans les colonnes qui n'existent pas
        $article = Article::create([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'categorie' => $request->categorie,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'prix_unitaire' => $request->prix_vente,
            'benefice' => $request->prix_vente - $request->prix_achat,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
            'unite_mesure' => $request->unite_mesure,
            'fournisseur' => $request->fournisseur,
            'emplacement' => $request->emplacement,
        ]);

        if ($request->stock > 0) {
            Stock::create([
                'article_id' => $article->id,
                'type' => 'entree',
                'quantite' => $request->stock,
                'prix_unitaire' => $request->prix_achat,
                'motif' => 'Création article',
                'date_mouvement' => now(),
                'stock_avant' => 0,
                'stock_apres' => $request->stock,
            ]);
        }

        return redirect()->route('articles.index')->with('success', 'Article créé avec succès !');
    }

    public function show(Article $article)
    {
        $stocks = $article->stocks()->orderBy('created_at', 'desc')->get();
        return view('articles.show', compact('article', 'stocks'));
    }

    public function edit(Article $article)
    {
        $categories = [
            'Rangement et organisation' => '📦 Pour le rangement et l\'organisation',
            'Géométrie et travaux manuels' => '📐 Pour la géométrie et les travaux manuels',
            'Coloriage et surlignage' => '🎨 Pour colorier et surligner',
            'Écriture et correction' => '✍️ Pour écrire et corriger',
            'Lecture et apprentissage' => '📚 Pour lire et apprendre'
        ];
        $unites = ['pièce', 'kg', 'g', 'mètre', 'cm', 'lot', 'boîte', 'paquet'];
        return view('articles.edit', compact('article', 'categories', 'unites'));
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'nom_article' => 'required|string|max:255|unique:articles,nom_article,' . $article->id,
            'code_barre' => 'nullable|string|max:50|unique:articles,code_barre,' . $article->id,
            'categorie' => 'required|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
            'unite_mesure' => 'required|string',
            'fournisseur' => 'nullable|string|max:255',
            'emplacement' => 'nullable|string|max:100',
        ]);

        $article->update([
            'nom_article' => $request->nom_article,
            'code_barre' => $request->code_barre,
            'categorie' => $request->categorie,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'prix_unitaire' => $request->prix_vente,
            'benefice' => $request->prix_vente - $request->prix_achat,
            'stock' => $request->stock,
            'seuil_alerte' => $request->seuil_alerte ?? 5,
            'unite_mesure' => $request->unite_mesure,
            'fournisseur' => $request->fournisseur,
            'emplacement' => $request->emplacement,
        ]);

        return redirect()->route('articles.index')->with('success', 'Article modifié avec succès !');
    }

    public function destroy(Article $article)
    {
        if ($article->kits()->count() > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article est utilisé dans des kits.');
        }
        if ($article->stocks()->count() > 0) {
            return redirect()->route('articles.index')->with('error', 'Cet article a des mouvements de stock.');
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article supprimé !');
    }

    public function exportCSV()
    {
        $articles = Article::all();
        $filename = storage_path('app/temp/articles.csv');

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $file = fopen($filename, 'w');
        fputcsv($file, [
            'ID', 'Code barre', 'Nom', 'Catégorie', "Prix d'achat",
            'Prix de vente', 'Bénéfice', 'Stock', 'Seuil', 'Unité',
            'Fournisseur', 'Emplacement'
        ]);

        foreach ($articles as $article) {
            fputcsv($file, [
                $article->id,
                $article->code_barre,
                $article->nom_article,
                $article->categorie,
                $article->prix_achat,
                $article->prix_vente,
                $article->benefice,
                $article->stock,
                $article->seuil_alerte,
                $article->unite_mesure,
                $article->fournisseur ?? '-',
                $article->emplacement ?? '-'
            ]);
        }
        fclose($file);

        return response()->download($filename, 'articles-' . date('Y-m-d') . '.csv')->deleteFileAfterSend(true);
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'fichier' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('fichier');
        $handle = fopen($file->path(), 'r');

        // Lire les en-têtes
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return redirect()->back()->with('error', '❌ Fichier CSV vide ou corrompu.');
        }

        // Nettoyer les en-têtes
        $header = array_map('trim', $header);

        // VÉRIFIER SI LE FICHIER EST COMPATIBLE
        $requiredHeaders = ['Nom', 'Catégorie', "Prix d'achat", 'Prix de vente', 'Stock'];
        $missingHeaders = [];
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $header)) {
                $missingHeaders[] = $required;
            }
        }

        if (!empty($missingHeaders)) {
            fclose($handle);
            return redirect()->back()->with('error', '❌ FICHIER NON COMPATIBLE ! En-têtes manquants : ' . implode(', ', $missingHeaders) . '. Utilisez le modèle d\'exportation.');
        }

        $count = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            try {
                $data = array_combine($header, $row);

                // Vérifier les champs obligatoires
                if (empty($data['Nom'])) {
                    $errors[] = "Ligne $rowNumber: Nom manquant";
                    continue;
                }

                Article::updateOrCreate(
                    ['code_barre' => $data['Code barre'] ?? null],
                    [
                        'nom_article' => $data['Nom'] ?? '',
                        'categorie' => $data['Catégorie'] ?? 'Rangement et organisation',
                        'prix_achat' => floatval($data["Prix d'achat"] ?? 0),
                        'prix_vente' => floatval($data['Prix de vente'] ?? 0),
                        'prix_unitaire' => floatval($data['Prix de vente'] ?? 0),
                        'benefice' => floatval($data['Prix de vente'] ?? 0) - floatval($data["Prix d'achat"] ?? 0),
                        'stock' => intval($data['Stock'] ?? 0),
                        'seuil_alerte' => intval($data['Seuil'] ?? 5),
                        'unite_mesure' => $data['Unité'] ?? 'pièce',
                        'fournisseur' => $data['Fournisseur'] ?? null,
                        'emplacement' => $data['Emplacement'] ?? null,
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Ligne $rowNumber: " . $e->getMessage();
            }
        }
        fclose($handle);

        if ($count == 0 && !empty($errors)) {
            return redirect()->back()->with('error', '❌ Aucun article importé. Vérifiez que votre fichier est au bon format.');
        }

        $message = "✅ $count articles importés avec succès !";
        if (!empty($errors)) {
            $message .= ' ⚠️ Erreurs: ' . implode('; ', $errors);
        }

        return redirect()->route('articles.index')->with('success', $message);
    }
}