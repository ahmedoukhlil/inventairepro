<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter la permission users.manage
        DB::table('permissions')->updateOrInsert(
            ['key' => 'users.manage'],
            [
                'label'       => 'Gérer les utilisateurs',
                'description' => 'Permet de créer, modifier et supprimer des comptes utilisateurs.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        // L'attacher au rôle admin uniquement
        $adminId      = DB::table('roles')->where('key', 'admin')->value('id');
        $permissionId = DB::table('permissions')->where('key', 'users.manage')->value('id');

        if ($adminId && $permissionId) {
            DB::table('role_permission')->updateOrInsert(
                ['role_id' => $adminId, 'permission_id' => $permissionId],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('key', 'users.manage')->value('id');

        if ($permissionId) {
            DB::table('role_permission')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }
    }
};
