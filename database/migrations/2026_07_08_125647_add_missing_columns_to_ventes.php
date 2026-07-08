<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            if (!Schema::hasColumn('ventes', 'quantite')) {
                $table->integer('quantite')->default(1);
            }
            if (!Schema::hasColumn('ventes', 'remise')) {
                $table->decimal('remise', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('ventes', 'frais_livraison')) {
                $table->decimal('frais_livraison', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('ventes', 'frais_carnet')) {
                $table->decimal('frais_carnet', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('ventes', 'mode_paiement')) {
                $table->string('mode_paiement')->nullable();
            }
            if (!Schema::hasColumn('ventes', 'reference_paiement')) {
                $table->string('reference_paiement')->nullable();
            }
            if (!Schema::hasColumn('ventes', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('ventes', 'type_vente')) {
                $table->enum('type_vente', ['article', 'kit'])->default('kit');
            }
            if (!Schema::hasColumn('ventes', 'article_id')) {
                $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn([
                'quantite', 'remise', 'frais_livraison', 'frais_carnet',
                'mode_paiement', 'reference_paiement', 'notes', 'type_vente', 'article_id'
            ]);
        });
    }
};