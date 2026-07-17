<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la colonne existe avant de la modifier
        if (Schema::hasColumn('articles', 'prix_unitaire')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->decimal('prix_unitaire', 15, 2)->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('articles', 'prix_unitaire')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->decimal('prix_unitaire', 15, 2)->nullable()->change();
            });
        }
    }
};