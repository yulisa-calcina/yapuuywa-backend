<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario administrador
        DB::table('users')->insertOrIgnore([
            'nombre'     => 'Administrador',
            'dni'        => '12345678',
            'password'   => Hash::make('admin123'),
            'rol'        => 'admin',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear usuario ganadero
        DB::table('users')->insertOrIgnore([
            'nombre'     => 'Yulisa Calcina',
            'dni'        => '73821116',
            'password'   => Hash::make('ganadero123'),
            'rol'        => 'ganadero',
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}