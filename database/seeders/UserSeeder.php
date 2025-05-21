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
                'name' => 'John Doe',
                'email' => 'user@gmail.com',
                'role' => 'user',
                'country' => 'USA',
                'description' => 'A regular user',
                'password' => Hash::make('12345678'),
                'avatar' => null,
                'status' => 'active',
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'country' => 'USA',
                'description' => 'An admin user',
                'password' => Hash::make('12345678'),
                'avatar' => null,
                'status' => 'active',
            ],
        ]);
    }
}
