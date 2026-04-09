<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_entrees', function (Blueprint $table) {
            $table->dropForeign(['fournisseur_id']);
            $table->dropIndex('idx_entree_fournisseur');
            $table->unsignedBigInteger('fournisseur_id')->nullable()->change();
            $table->foreign('fournisseur_id')->references('id')->on('stock_fournisseurs')->onDelete('restrict');
            $table->index('fournisseur_id', 'idx_entree_fournisseur');
        });
    }

    public function down(): void
    {
        Schema::table('stock_entrees', function (Blueprint $table) {
            $table->dropForeign(['fournisseur_id']);
            $table->dropIndex('idx_entree_fournisseur');
            $table->unsignedBigInteger('fournisseur_id')->nullable(false)->change();
            $table->foreign('fournisseur_id')->references('id')->on('stock_fournisseurs')->onDelete('restrict');
            $table->index('fournisseur_id', 'idx_entree_fournisseur');
        });
    }
};
