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
            'phone'         => '+1 (860) 910-2401',
            'email'         => 'shaylafayfayb@gmail.com',
            'name'          => 'Shayla Breault',
            'copyright'     => 'Copyright © 2025 BreathEasy. All rights reserved.',
            'description'   => "BreatheEasy is your go-to yoga app where you can subscribe to explore a variety of yoga styles—including Hatha, Vinyasa,
                                and Restorative—designed in different lengths to suit your schedule and energy. Whether you’re seeking movement, stillness, or connection,
                                this app brings it all into one space.Inside the app, you can post and share mindful insights, engage with others, and find motivation
                                through a supportive, like-minded community. This is more than just an app—it’s your space to pause, reflect, and breathe easy, every single day.",
            'address'       => 'USA',
            'keywords'      => 'yoga, Wellness, Hatha yoga , Vinyasa flow, restorative, meditation, mindfulness, community, daily yoga , breath easy',
            'author'        => 'Puppy',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }
}
