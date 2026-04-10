<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Assigne la permission stock.dashboard à tous les rôles ayant accès au stock :
     * admin, admin_stock, agent_stock
     */
    public function up(): void
    {
        $permission = DB::table('permissions')->where('key', 'stock.dashboard')->first();

        if (!$permission) {
            return;
        }

        $roles = DB::table('roles')->whereIn('key', ['admin', 'admin_stock', 'agent_stock'])->pluck('id');

        foreach ($roles as $roleId) {
            $exists = DB::table('role_permission')
                ->where('role_id', $roleId)
                ->where('permission_id', $permission->id)
                ->exists();

            if (!$exists) {
                DB::table('role_permission')->insert([
                    'role_id'       => $roleId,
                    'permission_id' => $permission->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $permission = DB::table('permissions')->where('key', 'stock.dashboard')->first();

        if (!$permission) {
            return;
        }

        $roles = DB::table('roles')->whereIn('key', ['admin', 'admin_stock', 'agent_stock'])->pluck('id');

        DB::table('role_permission')
            ->whereIn('role_id', $roles)
            ->where('permission_id', $permission->id)
            ->delete();
    }
};
