<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'All',
            'Stress relief' ,
            'Improve flexibility',
            'Improve posture',
            'Improve balance',
            'Improve strength',
        ];

        foreach ($data as $value) {
            \App\Models\ContentType::create([
                'title' => $value,
            ]);
        }
    }
}
