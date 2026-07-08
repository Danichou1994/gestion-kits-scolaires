<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kits', function (Blueprint $table) {
            // Rendre les colonnes nullable
            $table->decimal('reduction', 10, 2)->nullable()->change();
            $table->decimal('frais_livraison', 10, 2)->nullable()->change();
            $table->decimal('frais_carnet', 10, 2)->nullable()->change();
            $table->decimal('frais_emballage', 10, 2)->nullable()->change();
            $table->decimal('frais_etiquette', 10, 2)->nullable()->change();
            $table->decimal('prix_final', 20, 2)->nullable()->change();
            $table->boolean('en_promotion')->nullable()->change();
        });
    }

    public function down()
    {
        // Ne pas revenir en arrière
    }
};