<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->enum('type_mouvement', ['entree', 'sortie']);
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->foreignId('vente_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->nullable();
            $table->text('motif')->nullable();
            $table->date('date_mouvement');
            $table->integer('stock_avant')->default(0);
            $table->integer('stock_apres')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};