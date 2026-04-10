<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Assigne dashboard.view à admin et agent.
     * Assigne stock.dashboard à admin, admin_stock, agent_stock.
     */
    public function up(): void
    {
        $dashboardPerm = DB::table('permissions')->where('key', 'dashboard.view')->first();
        if ($dashboardPerm) {
            foreach (DB::table('roles')->whereIn('key', ['admin', 'agent'])->pluck('id') as $roleId) {
                DB::table('role_permission')->insertOrIgnore([
                    'role_id'       => $roleId,
                    'permission_id' => $dashboardPerm->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        $stockDashPerm = DB::table('permissions')->where('key', 'stock.dashboard')->first();
        if ($stockDashPerm) {
            foreach (DB::table('roles')->whereIn('key', ['admin', 'admin_stock', 'agent_stock'])->pluck('id') as $roleId) {
                DB::table('role_permission')->insertOrIgnore([
                    'role_id'       => $roleId,
                    'permission_id' => $stockDashPerm->id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        foreach (['dashboard.view', 'stock.dashboard'] as $key) {
            $perm = DB::table('permissions')->where('key', $key)->first();
            if ($perm) {
                DB::table('role_permission')->where('permission_id', $perm->id)->delete();
            }
        }
    }
};
