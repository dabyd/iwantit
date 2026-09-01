<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $menuItems = [
            [
                'name' => 'users',
                'label' => 'Users',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'projects',
                'label' => 'Projects',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'hotpoints',
                'label' => 'Hotpoints',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'tags',
                'label' => 'Tags',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'territories',
                'label' => 'Territories',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'brands',
                'label' => 'Brands',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'products',
                'label' => 'Products',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'options',
                'label' => 'Security items',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'datision-parameters',
                'label' => 'AI Machine CFG',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'roles',
                'label' => 'Roles',
                'permissions' => ['menu', 'screen', 'list', 'create', 'edit', 'delete', 'view'],
            ],
            [
                'name' => 'permissions',
                'label' => 'Permissions',
                'permissions' => ['menu', 'screen', 'list', 'view'],
            ],
            [
                'name' => 'analysis',
                'label' => 'Analysis',
                'permissions' => ['menu', 'screen', 'list', 'view'],
            ],
        ];

        foreach ($menuItems as $item) {
            foreach ($item['permissions'] as $permission) {
                $permissionName = $item['name'].'-'.$permission;

                Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['guard_name' => 'web']
                );
            }
        }

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->syncPermissions($allPermissions);
        }

        $this->command->info('Menu permissions seeded successfully!');
        $this->command->info('Total permissions created: '.Permission::count());
    }
}
