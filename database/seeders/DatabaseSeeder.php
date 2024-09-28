<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call(UserRoleSeeder::class);
        $adminRole = Role::create(['name' => 'admin']);

        $editArticlesPermission = Permission::create(['name' => 'edit tasks']);

        $adminRole->givePermissionTo($editArticlesPermission);

        $user = User::find(1);
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
