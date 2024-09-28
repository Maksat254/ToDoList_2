<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        if (!Role::where('name', 'admin')->exists()) {
            $adminRole = Role::create(['name' => 'admin']);
        }



        if (!Role::where('name', 'user')->exists()) {
            $userRole = Role::create(['name' => 'user']);
        }


        if (!Role::where('name', 'editor')->exists()) {
            $editorRole = Role::create(['name' => 'editor']);
        } else {
            $editorRole = Role::where('name', 'editor')->first();
        }


        $permissions = [
            'edit task',
            'delete task',
            'publish task',
            'unpublish task',
        ];

        foreach ($permissions as $permission) {

            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        $adminRole->givePermissionTo(Permission::all());


        $editorRole->givePermissionTo(['edit task', 'publish task']);
    }
}
