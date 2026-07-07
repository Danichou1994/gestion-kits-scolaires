<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'code_barre')) {
                $table->string('code_barre')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('articles', 'prix_achat')) {
                $table->decimal('prix_achat', 15, 2)->default(0)->after('prix_unitaire');
            }
            if (!Schema::hasColumn('articles', 'prix_vente')) {
                $table->decimal('prix_vente', 15, 2)->default(0)->after('prix_achat');
            }
            if (!Schema::hasColumn('articles', 'benefice')) {
                $table->decimal('benefice', 15, 2)->default(0)->after('prix_vente');
            }
            if (!Schema::hasColumn('articles', 'fournisseur')) {
                $table->string('fournisseur')->nullable()->after('categorie');
            }
            if (!Schema::hasColumn('articles', 'unite_mesure')) {
                $table->string('unite_mesure')->default('pièce')->after('fournisseur');
            }
            if (!Schema::hasColumn('articles', 'emplacement')) {
                $table->string('emplacement')->nullable()->after('unite_mesure');
            }
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'code_barre', 'prix_achat', 'prix_vente', 'benefice',
                'fournisseur', 'unite_mesure', 'emplacement'
            ]);
        });
    }
};