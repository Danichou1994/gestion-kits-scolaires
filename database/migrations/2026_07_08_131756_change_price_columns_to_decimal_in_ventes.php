<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->decimal('montant_total', 20, 2)->change();
            $table->decimal('acompte', 20, 2)->change();
            $table->decimal('solde', 20, 2)->change();
            $table->decimal('montant_mensualite', 20, 2)->change();
            $table->decimal('remise', 20, 2)->change();
            $table->decimal('frais_livraison', 20, 2)->change();
            $table->decimal('frais_carnet', 20, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->integer('montant_total')->change();
            $table->integer('acompte')->change();
            $table->integer('solde')->change();
            $table->integer('montant_mensualite')->change();
            $table->integer('remise')->change();
            $table->integer('frais_livraison')->change();
            $table->integer('frais_carnet')->change();
        });
    }
};