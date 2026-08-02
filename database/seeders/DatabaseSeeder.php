<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'nombre'     => 'Administrador',
            'dni'        => '12345678',
            'password'   => Hash::make('admin123'),
            'rol'        => 'admin',
            'genero'     => 'masculino',
            'foto_url'   => null,
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insertOrIgnore([
            'nombre'     => 'Yulisa Calcina',
            'dni'        => '73821116',
            'password'   => Hash::make('ganadero123'),
            'rol'        => 'ganadero',
            'genero'     => 'femenino',
            'foto_url'   => null,
            'activo'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}