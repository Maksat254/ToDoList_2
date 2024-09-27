<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
            $user = User::where('email', 'ernazarovmaksat4@gmail.com')->first();

        if ($user) {
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
                echo "Роль 'admin' успешно назначена пользователю.\n";
            } else {
                echo "Пользователь уже имеет роль 'admin'.\n";
            }
        } else {
            echo "Пользователь не найден.\n";
        }
    }
}
