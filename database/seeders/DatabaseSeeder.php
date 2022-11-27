<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
    }
}
