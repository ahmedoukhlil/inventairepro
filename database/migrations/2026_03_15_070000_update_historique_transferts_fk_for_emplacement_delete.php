<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('historique_transferts', function (Blueprint $table) {
            $table->dropForeign(['nouveau_idEmplacement']);
        });

        DB::statement('ALTER TABLE historique_transferts MODIFY nouveau_idEmplacement INT NULL');

        Schema::table('historique_transferts', function (Blueprint $table) {
            $table->foreign('nouveau_idEmplacement')
                ->references('idEmplacement')
                ->on('emplacement')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_transferts', function (Blueprint $table) {
            $table->dropForeign(['nouveau_idEmplacement']);
        });

        DB::statement('ALTER TABLE historique_transferts MODIFY nouveau_idEmplacement INT NOT NULL');

        Schema::table('historique_transferts', function (Blueprint $table) {
            $table->foreign('nouveau_idEmplacement')
                ->references('idEmplacement')
                ->on('emplacement')
                ->onDelete('restrict');
        });
    }
};

