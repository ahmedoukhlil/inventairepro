<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Créer la permission
        DB::table('permissions')->insertOrIgnore([
            'key'         => 'stock.delete_operations',
            'label'       => 'Supprimer opérations stock',
            'description' => 'Permet de supprimer des entrées et sorties de stock (ajuste le stock automatiquement).',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Assigner uniquement à admin_stock
        $permission = DB::table('permissions')->where('key', 'stock.delete_operations')->first();
        $role = DB::table('roles')->where('key', 'admin_stock')->first();

        if ($permission && $role) {
            DB::table('role_permission')->insertOrIgnore([
                'role_id'       => $role->id,
                'permission_id' => $permission->id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        $permission = DB::table('permissions')->where('key', 'stock.delete_operations')->first();
        if ($permission) {
            DB::table('role_permission')->where('permission_id', $permission->id)->delete();
            DB::table('permissions')->where('id', $permission->id)->delete();
        }
    }
};
