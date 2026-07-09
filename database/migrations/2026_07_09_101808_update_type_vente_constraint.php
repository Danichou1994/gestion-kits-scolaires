<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Modifier directement la colonne pour accepter 'mixte'
        DB::statement("ALTER TABLE ventes MODIFY type_vente ENUM('article', 'kit', 'mixte') NOT NULL DEFAULT 'kit'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE ventes MODIFY type_vente ENUM('article', 'kit') NOT NULL DEFAULT 'kit'");
    }
};