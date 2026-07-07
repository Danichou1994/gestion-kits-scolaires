<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->decimal('prix_achat', 20, 2)->change();
            $table->decimal('prix_vente', 20, 2)->change();
            $table->decimal('prix_unitaire', 20, 2)->change();
            $table->decimal('benefice', 20, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->decimal('prix_achat', 15, 2)->change();
            $table->decimal('prix_vente', 15, 2)->change();
            $table->decimal('prix_unitaire', 15, 2)->change();
            $table->decimal('benefice', 15, 2)->change();
        });
    }
};