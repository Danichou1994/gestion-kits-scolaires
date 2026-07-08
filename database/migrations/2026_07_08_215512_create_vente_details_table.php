<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vente_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['article', 'kit']);
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kit_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('quantite')->default(1);
            $table->decimal('prix_unitaire', 20, 2);
            $table->decimal('montant_ht', 20, 2);
            $table->decimal('tva', 20, 2)->default(0);
            $table->decimal('total_ligne', 20, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vente_details');
    }
};