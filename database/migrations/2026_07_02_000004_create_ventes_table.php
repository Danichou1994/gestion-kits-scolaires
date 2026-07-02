<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('kit_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('montant_total');
            $table->integer('acompte');
            $table->integer('solde');
            $table->integer('nb_mensualites')->default(3);
            $table->integer('montant_mensualite');
            $table->enum('statut', ['en_cours', 'termine', 'annule'])->default('en_cours');
            $table->date('date_vente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};