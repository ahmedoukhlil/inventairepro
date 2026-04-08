<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // Seed permissions (idempotent)
        $permissions = [
            [
                'key' => 'inventaire.access',
                'label' => 'Accès inventaire (web + PWA)',
                'description' => 'Permet d’accéder aux écrans inventaire/immobilisations et d’utiliser la PWA de scan.'
            ],
            [
                'key' => 'stock.access',
                'label' => 'Accès module stock',
                'description' => 'Permet d’afficher/consulter les écrans stock.'
            ],
            [
                'key' => 'stock.manage_references',
                'label' => 'Gérer références stock (magasins/catégories/...)',
                'description' => 'Permet de gérer les paramètres de stock et références.'
            ],
            [
                'key' => 'stock.create_entree',
                'label' => 'Créer entrées de stock',
                'description' => 'Permet d’enregistrer des entrées.'
            ],
            [
                'key' => 'stock.create_sortie',
                'label' => 'Créer sorties de stock',
                'description' => 'Permet d’enregistrer des sorties.'
            ],
            [
                'key' => 'stock.view_all_movements',
                'label' => 'Voir tous les mouvements',
                'description' => 'Permet de voir les mouvements créés par tous les utilisateurs.'
            ],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(
                ['key' => $p['key']],
                ['label' => $p['label'], 'description' => $p['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Seed roles (idempotent)
        $roles = [
            [
                'key' => 'admin',
                'label' => 'Superadmin',
                'description' => 'Statistiques stock uniquement, pas d’entrées/sorties.'
            ],
            [
                'key' => 'admin_stock',
                'label' => 'Admin Stock',
                'description' => 'Gestion complète stock, pas accès inventaire.'
            ],
            [
                'key' => 'agent',
                'label' => 'Agent inventaire',
                'description' => 'Scan via PWA seulement (inventaire), pas accès stock.'
            ],
            [
                'key' => 'agent_stock',
                'label' => 'Agent stock',
                'description' => 'Créer entrées + sorties, pas gestion références, pas accès inventaire.'
            ],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(
                ['key' => $r['key']],
                ['label' => $r['label'], 'description' => $r['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // Attach permissions to roles
        $roleKeyToPermissionKeys = [
            'admin' => [
                'inventaire.access' => true,
                'stock.access' => true,
                'stock.manage_references' => false,
                'stock.create_entree' => false,
                'stock.create_sortie' => false,
                'stock.view_all_movements' => true,
            ],
            'admin_stock' => [
                'inventaire.access' => false,
                'stock.access' => true,
                'stock.manage_references' => true,
                'stock.create_entree' => true,
                'stock.create_sortie' => true,
                'stock.view_all_movements' => true,
            ],
            'agent' => [
                'inventaire.access' => true,
                'stock.access' => false,
                'stock.manage_references' => false,
                'stock.create_entree' => false,
                'stock.create_sortie' => false,
                'stock.view_all_movements' => false,
            ],
            'agent_stock' => [
                'inventaire.access' => false,
                'stock.access' => true,
                'stock.manage_references' => false,
                'stock.create_entree' => true,
                'stock.create_sortie' => true,
                'stock.view_all_movements' => false,
            ],
        ];

        foreach ($roleKeyToPermissionKeys as $roleKey => $permMap) {
            $roleId = DB::table('roles')->where('key', $roleKey)->value('id');
            if (!$roleId) {
                continue;
            }

            foreach ($permMap as $permissionKey => $enabled) {
                if ($enabled) {
                    $permissionId = DB::table('permissions')->where('key', $permissionKey)->value('id');
                    if ($permissionId) {
                        DB::table('role_permission')->updateOrInsert(
                            ['role_id' => $roleId, 'permission_id' => $permissionId],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

