<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'f_name' => 'John',
                'l_name' => 'Doe',
                'email' => 'user@gmail.com',
                'role' => 'user',
                'password' => Hash::make('12345678'),
                'avatar' => null,
                'status' => 'active',
            ],
            [
                'f_name' => 'Admin',
                'l_name' => 'Admin',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'password' => Hash::make('12345678'),
                'avatar' => null,
                'status' => 'active',
            ],
        ]);
    }
}
