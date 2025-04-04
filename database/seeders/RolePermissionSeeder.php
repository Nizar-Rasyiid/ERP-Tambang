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
     */
    public function run(): void
    {
        $categories = ['SCM', 'MASTER-DATA', 'FINANCE', 'SALES', 'ACCESS'];
        $actions = ['view', 'create', 'delete'];

        foreach($categories as $category){
            foreach($actions as $action){
                Permission::create([
                    'name' => "{$action} {$category}"                    
                ]);
            }
        }

        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);
        $userRole = Role::create([
            'name' => 'user',
            'guard_name' => 'web'
        ]);

        $adminRole->givePermissionTo(Permission::all());
        $userRole->givePermissionTo(Permission::all());
    }
}
