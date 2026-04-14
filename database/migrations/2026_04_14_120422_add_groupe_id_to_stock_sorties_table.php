<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_sorties', function (Blueprint $table) {
            // UUID qui regroupe toutes les lignes d'une même commande
            $table->string('groupe_id', 36)->nullable()->after('created_by')->index();
        });
    }

    public function down(): void
    {
        Schema::table('stock_sorties', function (Blueprint $table) {
            $table->dropColumn('groupe_id');
        });
    }
};