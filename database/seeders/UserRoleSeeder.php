<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        $user = User::firstOrCreate(
            ['email' => 'ernazarovmaksat4@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password')
            ]
        );

        if (!$user->hasRole('admin')) {
            $user->assignRole('admin');
            echo "Роль 'admin' успешно назначена пользователю.\n";
        } else {
            echo "Пользователь уже имеет роль 'admin'.\n";
        }
    }
}
