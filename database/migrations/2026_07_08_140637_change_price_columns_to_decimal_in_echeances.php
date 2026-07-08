<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('echeances', function (Blueprint $table) {
            $table->decimal('montant_dû', 20, 2)->change();
        });
    }

    public function down()
    {
        Schema::table('echeances', function (Blueprint $table) {
            $table->integer('montant_dû')->change();
        });
    }
};