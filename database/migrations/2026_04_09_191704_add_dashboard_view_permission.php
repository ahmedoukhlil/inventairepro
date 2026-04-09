<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->updateOrInsert(
            ['key' => 'dashboard.view'],
            [
                'label'       => 'Voir le dashboard',
                'description' => 'Permet d\'accéder au tableau de bord principal.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        DB::table('permissions')->updateOrInsert(
            ['key' => 'stock.dashboard'],
            [
                'label'       => 'Voir le dashboard stock',
                'description' => 'Permet d\'accéder au tableau de bord du module stock.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('key', ['dashboard.view', 'stock.dashboard'])->delete();
    }
};
