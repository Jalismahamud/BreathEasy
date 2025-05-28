<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
       
        for ($i = 1; $i <= 5; $i++) {
            Content::create([
                'title' => 'Sample Content ' . $i,
                'description' => 'This is a sample description for content ' . $i,
                'category_id' => 1, 
                'content_type_id' => 1, 
                'content_duration_id' => 1,
                'type' => 'begginner',
                'video' => 'uploads/contents/' . $i . '.mp4',
            ]);
        }
    }
}
