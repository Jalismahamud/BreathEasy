<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;


class ContentSeeder extends Seeder
{
    public function run(): void
    {

        $levels = ['Beginner', 'Intermediate', 'Advanced'];
        $contentTypeIds = [1, 2, 3, 4, 5];
        $count = 1;
        foreach ($levels as $level) {
            foreach ($contentTypeIds as $typeId) {
                if ($count > 20) break 2;
                \App\Models\Content::create([
                    'title' => 'Hatha Content ' . $count,
                    'description' => 'Description for Hatha content ' . $count,
                    'category_id' => 1,
                    'content_type_id' => $typeId,
                    'type' => $levels[($count-1)%3],
                    'video' => 'uploads/contents/hatha_' . $count . '.mp4',
                    'video_length' => '00:30:00',
                ]);
                $count++;
            }
        }

        $count = 1;
        foreach ($levels as $level) {
            foreach ($contentTypeIds as $typeId) {
                if ($count > 30) break 2;
                \App\Models\Content::create([
                    'title' => 'Vinyasa Content ' . $count,
                    'description' => 'Description for Vinyasa content ' . $count,
                    'category_id' => 2,
                    'content_type_id' => $typeId,
                    'type' => $levels[($count-1)%3],
                    'video' => 'uploads/contents/vinyasa_' . $count . '.mp4',
                    'video_length' => '00:20:00',
                ]);
                $count++;
            }
        }

        $count = 1;
        foreach ($levels as $level) {
            foreach ($contentTypeIds as $typeId) {
                if ($count > 10) break 2;
                \App\Models\Content::create([
                    'title' => 'Restorative Content ' . $count,
                    'description' => 'Description for Restorative content ' . $count,
                    'category_id' => 3,
                    'content_type_id' => $typeId,
                    'type' => $levels[($count-1)%3],
                    'video' => 'uploads/contents/restorative_' . $count . '.mp4',
                    'video_length' => '00:25:00',
                ]);
                $count++;
            }
        }

        $count = 1;
        foreach ($levels as $level) {
            foreach ($contentTypeIds as $typeId) {
                if ($count > 10) break 2;
                \App\Models\Content::create([
                    'title' => 'Yogic Bits Content ' . $count,
                    'description' => 'Description for Yogic Bits content ' . $count,
                    'category_id' => 4,
                    'content_type_id' => $typeId,
                    'type' => $levels[($count-1)%3],
                    'video' => 'uploads/contents/yogicbits_' . $count . '.mp4',
                    'video_length' => '00:35:00',
                ]);
                $count++;
            }
        }

        $count = 1;
        foreach ($levels as $level) {
            foreach ($contentTypeIds as $typeId) {
                if ($count > 10) break 2;
                \App\Models\Content::create([
                    'title' => 'Guided Meditation Content ' . $count,
                    'description' => 'Description for Guided Meditation content ' . $count,
                    'category_id' => 5,
                    'content_type_id' => $typeId,
                    'type' => $levels[($count-1)%3],
                    'video' => 'uploads/contents/guidedmeditation_' . $count . '.mp4',
                    'video_length' => '00:30:00',
                ]);
                $count++;
            }
        }
    }
}
