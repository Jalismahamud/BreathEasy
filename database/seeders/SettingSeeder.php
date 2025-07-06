<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            'title'         => 'BreathEasy',
            'phone'         => '123456789',
            'email'         => 'info@breatheasy.com',
            'name'          => 'netrocat',
            'copyright'     => 'Copyright © 2025 BreathEasy. All rights reserved.',
            'description'   => "BreathEasy is a digital agency that creates and shares innovative digital product experiences tailored for startups and small businesses.
                                Through this platform, our team showcases project updates, creative work, and industry insights—giving users a behind-the-scenes look at
                                how we bring digital ideas to life.",
            'address'       => 'Cairo, Australia',
            'keywords'      => 'BreathEasy',
            'author'        => 'Puppy',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
