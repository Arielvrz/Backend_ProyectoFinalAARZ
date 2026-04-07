<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Administrador total del sistema'],
            ['name' => 'bodeguero', 'description' => 'Control de entradas e inventario'],
            ['name' => 'despacho', 'description' => 'Control de salidas de productos'],
        ]);
    }
}
