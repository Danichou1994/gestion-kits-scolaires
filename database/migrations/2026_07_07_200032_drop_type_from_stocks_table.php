<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            if (Schema::hasColumn('stocks', 'type')) {
                $table->dropColumn('type');
            }
        });
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->enum('type', ['entree', 'sortie'])->nullable()->after('article_id');
        });
    }
};