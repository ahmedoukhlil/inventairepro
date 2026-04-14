<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_sorties', function (Blueprint $table) {
            $table->string('numero_commande', 20)->nullable()->after('groupe_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('stock_sorties', function (Blueprint $table) {
            $table->dropColumn('numero_commande');
        });
    }
};
