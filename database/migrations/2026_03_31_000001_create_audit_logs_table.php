<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event');                          // created | updated | deleted
            $table->string('auditable_type');                 // Nom du modèle (ex: App\Models\Gesimmo)
            $table->unsignedBigInteger('auditable_id');       // PK du modèle concerné
            $table->unsignedInteger('user_id')->nullable();   // idUser de l'auteur
            $table->string('user_name')->nullable();          // Snapshot nom utilisateur
            $table->string('ip_address', 45)->nullable();
            $table->json('old_values')->nullable();           // État avant modification
            $table->json('new_values')->nullable();           // État après modification
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
