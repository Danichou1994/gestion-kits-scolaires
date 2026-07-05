<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleCategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Rangement' => ['Classeur', 'Intercalaire', 'Chemise', 'Porte-documents', 'Boîte de rangement'],
            'Géométrie' => ['Règle', 'Équerre', 'Compas', 'Rapporteur', 'Ciseaux', 'Colle', 'Gomme', 'Taille-crayon'],
            'Coloriage' => ['Crayon de couleur', 'Feutre', 'Surligneur', 'Pastel', 'Crayon à papier'],
            'Écriture' => ['Stylo bleu', 'Stylo noir', 'Stylo rouge', 'Stylo vert', 'Correcteur', 'Effaceur'],
            'Apprentissage' => ['Cahier', 'Bloc-notes', 'Post-it', 'Marque-page', 'Calculatrice', 'Dictionnaire'],
        ];

        foreach ($categories as $categorie => $articles) {
            foreach ($articles as $nom) {
                Article::updateOrCreate(
                    ['nom_article' => $nom],
                    [
                        'categorie' => $categorie,
                        'prix_achat' => rand(50, 500),
                        'prix_vente' => rand(100, 1000),
                        'prix_unitaire' => rand(100, 1000),
                        'stock' => rand(10, 100),
                        'seuil_alerte' => 5,
                        'fournisseur' => 'Fournisseur ' . rand(1, 5),
                        'unite_mesure' => 'pièce'
                    ]
                );
            }
        }
    }
}