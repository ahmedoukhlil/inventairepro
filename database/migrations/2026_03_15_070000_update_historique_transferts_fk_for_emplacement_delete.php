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
        // Idempotent: en PROD la contrainte peut ne pas exister / avoir un nom différent
        try {
            $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
                ->where('TABLE_NAME', 'historique_transferts')
                ->where('COLUMN_NAME', 'nouveau_idEmplacement')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');

            if ($fkName) {
                DB::statement("ALTER TABLE `historique_transferts` DROP FOREIGN KEY `$fkName`");
            }
        } catch (\Throwable $e) {
            // Ne pas bloquer la migration si la plateforme ne permet pas l'accès à information_schema
        }

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
        // Idempotent: la contrainte peut ne pas exister / avoir un nom différent
        try {
            $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
                ->where('TABLE_NAME', 'historique_transferts')
                ->where('COLUMN_NAME', 'nouveau_idEmplacement')
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->value('CONSTRAINT_NAME');

            if ($fkName) {
                DB::statement("ALTER TABLE `historique_transferts` DROP FOREIGN KEY `$fkName`");
            }
        } catch (\Throwable $e) {
            // Ne pas bloquer
        }

        DB::statement('ALTER TABLE historique_transferts MODIFY nouveau_idEmplacement INT NOT NULL');

        Schema::table('historique_transferts', function (Blueprint $table) {
            $table->foreign('nouveau_idEmplacement')
                ->references('idEmplacement')
                ->on('emplacement')
                ->onDelete('restrict');
        });
    }
};

