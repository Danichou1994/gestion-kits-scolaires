<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('code_barre')->nullable()->unique()->after('id');
            $table->decimal('prix_achat', 10, 2)->default(0)->after('prix_unitaire');
            $table->decimal('prix_vente', 10, 2)->default(0)->after('prix_achat');
            $table->decimal('benefice', 10, 2)->default(0)->after('prix_vente');
            $table->string('fournisseur')->nullable()->after('categorie');
            $table->string('unite_mesure')->default('pièce')->after('fournisseur');
            $table->string('emplacement')->nullable()->after('unite_mesure');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['code_barre', 'prix_achat', 'prix_vente', 'benefice', 'fournisseur', 'unite_mesure', 'emplacement']);
        });
    }
};