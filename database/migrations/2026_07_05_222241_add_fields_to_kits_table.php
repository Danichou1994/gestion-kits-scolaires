<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->decimal('reduction', 10, 2)->default(0)->after('prix_total');
            $table->decimal('frais_livraison', 10, 2)->default(0)->after('reduction');
            $table->decimal('frais_carnet', 10, 2)->default(0)->after('frais_livraison');
            $table->decimal('prix_final', 10, 2)->default(0)->after('frais_carnet');
            $table->boolean('en_promotion')->default(false)->after('prix_final');
        });
    }

    public function down()
    {
        Schema::table('kits', function (Blueprint $table) {
            $table->dropColumn(['reduction', 'frais_livraison', 'frais_carnet', 'prix_final', 'en_promotion']);
        });
    }
};