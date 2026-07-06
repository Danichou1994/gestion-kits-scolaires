<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            if (!Schema::hasColumn('ventes', 'numero_vente')) {
                $table->string('numero_vente')->unique()->after('id');
            }
            if (!Schema::hasColumn('ventes', 'type_vente')) {
                $table->enum('type_vente', ['article', 'kit'])->default('kit')->after('client_id');
            }
            if (!Schema::hasColumn('ventes', 'article_id')) {
                $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete()->after('kit_id');
            }
            if (!Schema::hasColumn('ventes', 'quantite')) {
                $table->integer('quantite')->default(1)->after('article_id');
            }
            if (!Schema::hasColumn('ventes', 'remise')) {
                $table->decimal('remise', 10, 2)->default(0)->after('montant_total');
            }
            if (!Schema::hasColumn('ventes', 'frais_livraison')) {
                $table->decimal('frais_livraison', 10, 2)->default(0)->after('remise');
            }
            if (!Schema::hasColumn('ventes', 'frais_carnet')) {
                $table->decimal('frais_carnet', 10, 2)->default(0)->after('frais_livraison');
            }
            if (!Schema::hasColumn('ventes', 'mode_paiement')) {
                $table->string('mode_paiement')->nullable()->after('statut');
            }
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn([
                'numero_vente', 'type_vente', 'article_id', 'quantite',
                'remise', 'frais_livraison', 'frais_carnet', 'mode_paiement'
            ]);
        });
    }
};