<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Sur PostgreSQL, on supprime la contrainte et on modifie le type
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE ventes DROP CONSTRAINT IF EXISTS ventes_type_vente_check");
            DB::statement("ALTER TABLE ventes ALTER COLUMN type_vente TYPE text");
            DB::statement("ALTER TABLE ventes ALTER COLUMN type_vente SET DEFAULT 'kit'");
        } else {
            DB::statement("ALTER TABLE ventes MODIFY type_vente ENUM('article', 'kit', 'mixte') NOT NULL DEFAULT 'kit'");
        }
    }

    public function down()
    {
        // Rien
    }
};