<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario gestor admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@packsys.com',
            'password' => Hash::make('password'),
            'role' => 'gestor',
        ]);

        // Usuario vendedor de prueba
        User::create([
            'name' => 'Vendedor Test',
            'email' => 'vendedor@packsys.com',
            'password' => Hash::make('password'),
            'role' => 'vendedor',
        ]);

        // Usuario operario de prueba
        User::create([
            'name' => 'Operario Test',
            'email' => 'operario@packsys.com',
            'password' => Hash::make('password'),
            'role' => 'operario',
        ]);
    }
}
