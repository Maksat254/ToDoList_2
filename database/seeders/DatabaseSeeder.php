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
        // Создание роли 'admin'
        $adminRole = Role::create(['name' => 'admin']);

        // Создание прав
        $editArticlesPermission = Permission::create(['name' => 'edit tasks']);

        // Присваивание прав роли 'admin'
        $adminRole->givePermissionTo($editArticlesPermission);

        // Назначение роли администратору
        $user = User::find(1); // Найдите пользователя с ID 1
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
