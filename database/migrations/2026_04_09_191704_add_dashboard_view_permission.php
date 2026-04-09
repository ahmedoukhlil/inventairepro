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
    }

    public function down(): void
    {
        DB::table('permissions')->where('key', 'dashboard.view')->delete();
    }
};
