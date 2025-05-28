<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Hatha Yoga',
            'Vinyasa Yoga',
            'Restorative Yoga',
            'Yogic bits',
            'Guided Meditation'
        ];


        foreach ($data as $value) {
            \App\Models\Category::create([
                'title' => $value,
            ]);
        }
    }
}
