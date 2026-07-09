<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            if (Schema::hasColumn('ventes', 'total_tva')) {
                $table->dropColumn('total_tva');
            }
            if (Schema::hasColumn('ventes', 'total_ttc')) {
                $table->renameColumn('total_ttc', 'total_ht');
            }
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            if (!Schema::hasColumn('ventes', 'total_tva')) {
                $table->decimal('total_tva', 20, 2)->default(0);
            }
            if (Schema::hasColumn('ventes', 'total_ht')) {
                $table->renameColumn('total_ht', 'total_ttc');
            }
        });
    }
};