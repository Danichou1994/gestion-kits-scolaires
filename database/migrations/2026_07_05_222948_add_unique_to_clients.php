<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('email')->nullable()->after('telephone');
            $table->unique(['telephone', 'email']);
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique(['telephone', 'email']);
            $table->dropColumn('email');
        });
    }
};