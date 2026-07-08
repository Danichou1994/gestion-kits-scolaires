<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['kit_id', 'article_id', 'quantite']);
            $table->decimal('montant_ht', 20, 2)->default(0);
            $table->decimal('tva', 20, 2)->default(0);
            $table->decimal('remise', 20, 2)->default(0);
            $table->decimal('frais_livraison', 20, 2)->default(0);
            $table->decimal('frais_carnet', 20, 2)->default(0);
            $table->decimal('net_a_payer', 20, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->foreignId('kit_id')->nullable();
            $table->foreignId('article_id')->nullable();
            $table->integer('quantite')->default(1);
            $table->dropColumn(['montant_ht', 'tva', 'remise', 'frais_livraison', 'frais_carnet', 'net_a_payer']);
        });
    }
};