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
        Schema::create('localisations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('designation');
            $table->string('batiment')->nullable();
            $table->integer('etage')->nullable();
            $table->string('service_rattache')->nullable();
            $table->string('responsable')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();

            // Index
            $table->index('code');
            $table->index('service_rattache');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localisations');
    }
};
