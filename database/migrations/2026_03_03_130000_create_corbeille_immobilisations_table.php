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
        Schema::create('corbeille_immobilisations', function (Blueprint $table) {
            $table->id();
            $table->integer('original_num_ordre')->index();
            $table->integer('idDesignation');
            $table->integer('idCategorie');
            $table->integer('idEtat');
            $table->integer('idEmplacement');
            $table->integer('idNatJur');
            $table->integer('idSF');
            $table->date('DateAcquisition')->nullable();
            $table->text('Observations')->nullable();
            $table->text('barcode')->nullable();
            $table->string('designation_label', 255)->nullable();
            $table->string('deleted_reason', 255)->nullable();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->timestamp('deleted_at')->useCurrent();
            $table->timestamps();

            $table->index('deleted_at');
            $table->index('idDesignation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corbeille_immobilisations');
    }
};
