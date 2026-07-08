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
                $table->decimal('reduction', 10, 2)->default(0)->after('prix_total');
            }
            if (!Schema::hasColumn('kits', 'frais_livraison')) {
                $table->decimal('frais_livraison', 10, 2)->default(0)->after('reduction');
            }
            if (!Schema::hasColumn('kits', 'frais_carnet')) {
                $table->decimal('frais_carnet', 10, 2)->default(0)->after('frais_livraison');
            }
            if (!Schema::hasColumn('kits', 'prix_final')) {
                $table->decimal('prix_final', 20, 2)->default(0)->after('frais_carnet');
            }
            if (!Schema::hasColumn('kits', 'en_promotion')) {
                $table->boolean('en_promotion')->default(false)->after('prix_final');
            }
            if (!Schema::hasColumn('kits', 'date_debut_promo')) {
                $table->date('date_debut_promo')->nullable()->after('en_promotion');
            }
            if (!Schema::hasColumn('kits', 'date_fin_promo')) {
                $table->date('date_fin_promo')->nullable()->after('date_debut_promo');
            }
            if (!Schema::hasColumn('kits', 'frais_emballage')) {
                $table->decimal('frais_emballage', 10, 2)->default(0)->after('date_fin_promo');
            }
            if (!Schema::hasColumn('kits', 'frais_etiquette')) {
                $table->decimal('frais_etiquette', 10, 2)->default(0)->after('frais_emballage');
            }
            if (!Schema::hasColumn('kits', 'kit_notes')) {
                $table->text('kit_notes')->nullable()->after('frais_etiquette');
            }
        });
    }

    public function down()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn([
                'reduction', 'frais_livraison', 'frais_carnet',
                'prix_final', 'en_promotion', 'date_debut_promo',
                'date_fin_promo', 'frais_emballage', 'frais_etiquette',
                'kit_notes'
            ]);
        });
    }
};