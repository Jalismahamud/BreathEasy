<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentLengthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            '0-20 minutes',
            '20-30 minutes',
            '30-40 minutes',
            '40-50 minutes',
            '50-60 minutes',
        ];

        foreach ($data as $value) {
            \App\Models\ContentDuration::create([
                'length' => $value,
            ]);
        }

    }
}
