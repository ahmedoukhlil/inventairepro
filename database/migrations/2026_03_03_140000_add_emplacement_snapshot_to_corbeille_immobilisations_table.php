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
        Schema::table('corbeille_immobilisations', function (Blueprint $table) {
            $table->string('emplacement_label', 255)->nullable()->after('barcode');
            $table->string('emplacement_code', 255)->nullable()->after('emplacement_label');
            $table->integer('emplacement_id_affectation')->nullable()->after('emplacement_code');
            $table->integer('emplacement_id_localisation')->nullable()->after('emplacement_id_affectation');
            $table->string('affectation_label', 255)->nullable()->after('emplacement_id_localisation');
            $table->string('localisation_label', 255)->nullable()->after('affectation_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('corbeille_immobilisations', function (Blueprint $table) {
            $table->dropColumn([
                'emplacement_label',
                'emplacement_code',
                'emplacement_id_affectation',
                'emplacement_id_localisation',
                'affectation_label',
                'localisation_label',
            ]);
        });
    }
};

