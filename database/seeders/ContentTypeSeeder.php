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
            'strees relef' ,
            'improve flexibility',
            'improve posture',
            'improve balance',
            'improve strength',
        ];

        foreach ($data as $value) {
            \App\Models\ContentType::create([
                'title' => $value,
            ]);
        }
    }
}
