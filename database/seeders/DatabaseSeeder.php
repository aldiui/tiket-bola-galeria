<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'nama' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('11221122'),
        ]);

        DB::table('hak_akses')->insert([
            'user_id' => '1',
        ]);

        DB::table('pengaturans')->insert([
            'tarif' => '20000',
        ]);
    }
}
