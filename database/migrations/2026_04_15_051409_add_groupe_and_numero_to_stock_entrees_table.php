<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_entrees', function (Blueprint $table) {
            $table->string('groupe_id', 36)->nullable()->after('created_by')->index();
            $table->string('numero_entree', 20)->nullable()->after('groupe_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('stock_entrees', function (Blueprint $table) {
            $table->dropColumn(['groupe_id', 'numero_entree']);
        });
    }
};