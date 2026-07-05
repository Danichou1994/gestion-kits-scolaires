<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->decimal('reduction', 5, 2)->default(0)->after('prix_total');
            $table->decimal('prix_livraison', 10, 2)->default(0)->after('reduction');
            $table->decimal('prix_carnet', 10, 2)->default(0)->after('prix_livraison');
            $table->decimal('prix_final', 10, 2)->default(0)->after('prix_carnet');
            $table->boolean('est_promotion')->default(false)->after('prix_final');
            $table->date('date_debut_promo')->nullable()->after('est_promotion');
            $table->date('date_fin_promo')->nullable()->after('date_debut_promo');
        });
    }

    public function down()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn(['reduction', 'prix_livraison', 'prix_carnet', 'prix_final', 'est_promotion', 'date_debut_promo', 'date_fin_promo']);
        });
    }
};