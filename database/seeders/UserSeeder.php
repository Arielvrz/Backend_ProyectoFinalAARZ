<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('roles')->where('name', 'admin')->value('id');
        $bodegueroId = DB::table('roles')->where('name', 'bodeguero')->value('id');
        $despachoId = DB::table('roles')->where('name', 'despacho')->value('id');

        DB::table('users')->insert([
            [
                'name' => 'Administrador General',
                'email' => 'admin@empresa.com',
                'password' => Hash::make('password123'),
                'role_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan Bodega',
                'email' => 'bodega@empresa.com',
                'password' => Hash::make('password123'),
                'role_id' => $bodegueroId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ana Ventas',
                'email' => 'despacho@empresa.com',
                'password' => Hash::make('password123'),
                'role_id' => $despachoId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
