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
        Schema::create('collecte_biens_initiale', function (Blueprint $table) {
            $table->id();
            $table->uuid('lot_uid');
            $table->unsignedInteger('line_index');
            $table->string('emplacement_label', 255);
            $table->string('affectation_label', 255);
            $table->string('localisation_label', 255)->nullable();
            $table->string('designation', 255);
            $table->unsignedInteger('quantite')->default(1);
            $table->enum('etat', ['neuf', 'bon', 'moyen', 'mauvais'])->nullable();
            $table->integer('date_acquisition')->nullable();
            $table->text('observations')->nullable();
            $table->text('transcription_brute')->nullable();
            $table->decimal('confiance', 5, 2)->nullable();
            $table->string('agent_label', 255)->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            // Table autonome: aucun FK volontairement.
            $table->index('lot_uid');
            $table->index('emplacement_label');
            $table->index('created_at');
            $table->unique(['lot_uid', 'line_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collecte_biens_initiale');
    }
};
