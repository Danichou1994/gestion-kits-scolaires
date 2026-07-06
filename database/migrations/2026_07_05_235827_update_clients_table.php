<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Ajouter l'email seulement s'il n'existe pas
            if (!Schema::hasColumn('clients', 'email')) {
                $table->string('email')->nullable()->after('telephone');
            }
            
            // Ajouter l'index unique sans essayer de le supprimer d'abord
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