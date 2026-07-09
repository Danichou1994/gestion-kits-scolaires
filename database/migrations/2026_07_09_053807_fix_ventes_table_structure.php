<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            // Supprimer les contraintes de clé étrangère d'abord
            $table->dropForeign(['article_id']);
            $table->dropForeign(['kit_id']);
            
            // Ensuite supprimer les colonnes
            $table->dropColumn(['article_id', 'kit_id', 'quantite']);
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kit_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantite')->default(1);
        });
    }
};