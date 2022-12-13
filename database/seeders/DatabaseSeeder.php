<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table("rols")->insert([
            [
                "nombre" => "admin",
                "descripcion" => "administrador del sistema",
            ],
            [
                "nombre" => "cliente",
                "descripcion" => "cliente del sistema",
            ]
        ]);
        DB::table("usuarios")->insert([
            [
                "nombre" => "eduardo",
                "email" => "jorge.ecrg@gmail.com",
                "password" => Hash::make("12345678"),
                "perfil" => "admin.jpg",
                "rol_id" => 1,
            ]
        ]);
    }
}
