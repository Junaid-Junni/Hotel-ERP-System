<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view posts',
            'create posts',
            'edit posts',
            'delete posts',
            'manage users'
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles
        $roles = [
            'admin' => ['manage users', 'delete posts', 'edit posts', 'create posts', 'view posts'],
            'staff' => ['edit posts', 'create posts', 'view posts'],
            'user'  => ['view posts'],
        ];

        // Create roles and assign permissions
        foreach ($roles as $role => $rolePermissions) {
            $roleModel = Role::firstOrCreate(['name' => $role]);
            $roleModel->syncPermissions($rolePermissions);
        }
    
    }
}
