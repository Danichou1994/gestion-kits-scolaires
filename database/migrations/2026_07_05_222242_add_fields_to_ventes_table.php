<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->string('numero_vente')->unique()->after('id');
            $table->enum('type_vente', ['article', 'kit'])->default('kit')->after('client_id');
            $table->foreignId('article_id')->nullable()->constrained()->onDelete('set null')->after('kit_id');
            $table->integer('quantite_article')->default(1)->after('article_id');
            $table->decimal('montant_ht', 10, 2)->default(0)->after('montant_total');
            $table->decimal('tva', 10, 2)->default(0)->after('montant_ht');
            $table->decimal('remise', 10, 2)->default(0)->after('tva');
            $table->decimal('frais_livraison', 10, 2)->default(0)->after('remise');
            $table->decimal('frais_carnet', 10, 2)->default(0)->after('frais_livraison');
            $table->string('mode_paiement')->nullable()->after('statut');
            $table->string('reference_paiement')->nullable()->after('mode_paiement');
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['numero_vente', 'type_vente', 'article_id', 'quantite_article', 'montant_ht', 'tva', 'remise', 'frais_livraison', 'frais_carnet', 'mode_paiement', 'reference_paiement']);
        });
    }
};