<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            // Ajouter les colonnes manquantes sans supprimer les existantes
            if (!Schema::hasColumn('ventes', 'items')) {
                $table->json('items')->nullable();
            }
            if (!Schema::hasColumn('ventes', 'sous_total')) {
                $table->decimal('sous_total', 20, 2)->default(0);
            }
            if (!Schema::hasColumn('ventes', 'total_tva')) {
                $table->decimal('total_tva', 20, 2)->default(0);
            }
            if (!Schema::hasColumn('ventes', 'total_ttc')) {
                $table->decimal('total_ttc', 20, 2)->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['items', 'sous_total', 'total_tva', 'total_ttc']);
        });
    }
};