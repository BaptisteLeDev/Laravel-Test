<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'create formations',
            'edit formations',
            'delete formations',
            'edit assigned formations',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roles = [
            'admin' => ['create formations', 'edit formations', 'delete formations'],
            'ecole' => ['edit assigned formations'],
            'prof' => ['create formations', 'edit formations'],
            'etudiant' => [],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            if (! empty($perms)) {
                $role->syncPermissions($perms);
            }
        }
    }
}
