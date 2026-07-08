<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kits', function (Blueprint $table) {
            if (!Schema::hasColumn('kits', 'reduction')) {
                $table->decimal('reduction', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('kits', 'frais_livraison')) {
                $table->decimal('frais_livraison', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('kits', 'frais_carnet')) {
                $table->decimal('frais_carnet', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('kits', 'frais_emballage')) {
                $table->decimal('frais_emballage', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('kits', 'frais_etiquette')) {
                $table->decimal('frais_etiquette', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('kits', 'prix_final')) {
                $table->decimal('prix_final', 20, 2)->default(0);
            }
            if (!Schema::hasColumn('kits', 'en_promotion')) {
                $table->boolean('en_promotion')->default(false);
            }
            if (!Schema::hasColumn('kits', 'date_debut_promo')) {
                $table->date('date_debut_promo')->nullable();
            }
            if (!Schema::hasColumn('kits', 'date_fin_promo')) {
                $table->date('date_fin_promo')->nullable();
            }
            if (!Schema::hasColumn('kits', 'kit_notes')) {
                $table->text('kit_notes')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn([
                'reduction', 'frais_livraison', 'frais_carnet',
                'frais_emballage', 'frais_etiquette', 'prix_final',
                'en_promotion', 'date_debut_promo', 'date_fin_promo',
                'kit_notes'
            ]);
        });
    }
};