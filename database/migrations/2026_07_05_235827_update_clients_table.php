<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Vérifier si la colonne email existe déjà
            if (!Schema::hasColumn('clients', 'email')) {
                $table->string('email')->nullable()->after('telephone');
            }
            
            // Supprimer l'ancien unique si existe
            try {
                $table->dropUnique(['nom', 'prenom', 'telephone']);
            } catch (\Exception $e) {
                // L'index n'existe peut-être pas
            }
            
            // Ajouter le nouveau unique
            $table->unique(['nom', 'prenom', 'telephone']);
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['nom', 'prenom', 'telephone']);
            if (Schema::hasColumn('clients', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};