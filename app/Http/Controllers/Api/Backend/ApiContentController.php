<?php

namespace App\Http\Controllers\Api\Backend;

use Exception;
use App\Models\Content;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApiContentController extends Controller
{
    use ApiResponse;

    public function hathaYoga(Request $request)
    {

        try {
            $query = Content::with(['category', 'contentType'])
                ->where('category_id', 1);

            if ($request->filled('level') && $request->level !== 'all') {
                $query->where('type', $request->level);
            }
            if ($request->filled('duration') && $request->duration !== 'all') {
                $duration = $request->duration;
                if ($duration === '5-10') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 5 AND TIME_TO_SEC(video_length)/60 <= 10");
                } elseif ($duration === '20-30') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 20 AND TIME_TO_SEC(video_length)/60 <= 30");
                } elseif ($duration === '30-40') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 30 AND TIME_TO_SEC(video_length)/60 <= 40");
                } elseif ($duration === '40-60') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 40 AND TIME_TO_SEC(video_length)/60 <= 60");
                }
            }
            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('content_type_id', $request->type);
            }
            $results = $query->latest()->get();

            if ($results->isEmpty()) {
                return $this->success([
                    'category' => 'Hatha Yoga',
                    'contents' => []
                ], 'No content found.', 200);
            }

            $formatted = [
                'category' => $results->first()->category->title ?? 'Hatha Yoga',
                'contents' => $results->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'title'         => $item->title,
                        'description'   => $item->description,
                        'image'         => $item->image ? url($item->image) : null,
                        'video'         => $item->video ? url($item->video) : null,
                        'video_duration'=> $item->video_length ?? null,
                        'level'         => $item->type,
                        'type'          => $item->contentType->title ?? null,
                    ];
                })->values(),
            ];

            return $this->success($formatted, 'Content fetched successfully.', 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 422);
        }
    }

    public function vinyasaYoga(Request $request)
    {
        try {
            $query = Content::with(['category', 'contentType'])
                ->where('category_id', 2);

            if ($request->filled('level') && $request->level !== 'all') {
                $query->where('type', $request->level);
            }
            if ($request->filled('duration') && $request->duration !== 'all') {
                $duration = $request->duration;
                if ($duration === '5-10') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 5 AND TIME_TO_SEC(video_length)/60 <= 10");
                } elseif ($duration === '20-30') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 20 AND TIME_TO_SEC(video_length)/60 <= 30");
                } elseif ($duration === '30-40') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 30 AND TIME_TO_SEC(video_length)/60 <= 40");
                } elseif ($duration === '40-60') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 40 AND TIME_TO_SEC(video_length)/60 <= 60");
                }
            }
            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('content_type_id', $request->type);
            }
            $results = $query->latest()->get();

            if ($results->isEmpty()) {
                return $this->success([
                    'category' => 'Vinyasa Yoga',
                    'contents' => []
                ], 'No content found.', 200);
            }

            $formatted = [
                'category' => $results->first()->category->title ?? 'Vinyasa Yoga',
                'contents' => $results->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'title'         => $item->title,
                        'description'   => $item->description,
                        'image'         => $item->image ? url($item->image) : null,
                        'video'         => $item->video ? url($item->video) : null,
                        'video_duration'=> $item->video_length ?? null,
                        'level'         => $item->type,
                        'type'          => $item->contentType->title ?? null,
                    ];
                })->values(),
            ];

            return $this->success($formatted, 'Content fetched successfully.', 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 422);
        }
    }

    public function restorativeYoga(Request $request)
    {
        try {
            $query = Content::with(['category', 'contentType'])
                ->where('category_id', 3);

            if ($request->filled('level') && $request->level !== 'all') {
                $query->where('type', $request->level);
            }
            if ($request->filled('duration') && $request->duration !== 'all') {
                $duration = $request->duration;
                if ($duration === '5-10') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 5 AND TIME_TO_SEC(video_length)/60 <= 10");
                } elseif ($duration === '20-30') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 20 AND TIME_TO_SEC(video_length)/60 <= 30");
                } elseif ($duration === '30-40') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 30 AND TIME_TO_SEC(video_length)/60 <= 40");
                } elseif ($duration === '40-60') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 40 AND TIME_TO_SEC(video_length)/60 <= 60");
                }
            }
            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('content_type_id', $request->type);
            }
            $results = $query->latest()->get();

            if ($results->isEmpty()) {
                return $this->success([
                    'category' => 'Restorative Yoga',
                    'contents' => []
                ], 'No content found.', 200);
            }

            $formatted = [
                'category' => $results->first()->category->title ?? 'Restorative Yoga',
                'contents' => $results->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'title'         => $item->title,
                        'description'   => $item->description,
                        'image'         => $item->image ? url($item->image) : null,
                        'video'         => $item->video ? url($item->video) : null,
                        'video_duration'=> $item->video_length ?? null,
                        'level'         => $item->type,
                        'type'          => $item->contentType->title ?? null,
                    ];
                })->values(),
            ];

            return $this->success($formatted, 'Content fetched successfully.', 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 422);
        }
    }

    public function yogicBits(Request $request)
    {
        try {
            $query = Content::with(['category', 'contentType'])
                ->where('category_id', 4);

            if ($request->filled('level') && $request->level !== 'all') {
                $query->where('type', $request->level);
            }
            if ($request->filled('duration') && $request->duration !== 'all') {
                $duration = $request->duration;
                if ($duration === '5-10') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 5 AND TIME_TO_SEC(video_length)/60 <= 10");
                } elseif ($duration === '20-30') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 20 AND TIME_TO_SEC(video_length)/60 <= 30");
                } elseif ($duration === '30-40') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 30 AND TIME_TO_SEC(video_length)/60 <= 40");
                } elseif ($duration === '40-60') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 40 AND TIME_TO_SEC(video_length)/60 <= 60");
                }
            }
            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('content_type_id', $request->type);
            }
            $results = $query->latest()->get();

            if ($results->isEmpty()) {
                return $this->success([
                    'category' => 'Yogic bits',
                    'contents' => []
                ], 'No content found.', 200);
            }

            $formatted = [
                'category' => $results->first()->category->title ?? 'Yogic bits',
                'contents' => $results->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'title'         => $item->title,
                        'description'   => $item->description,
                        'image'         => $item->image ? url($item->image) : null,
                        'video'         => $item->video ? url($item->video) : null,
                        'video_duration'=> $item->video_length ?? null,
                        'level'         => $item->type,
                        'type'          => $item->contentType->title ?? null,
                    ];
                })->values(),
            ];

            return $this->success($formatted, 'Content fetched successfully.', 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 422);
        }
    }

    public function guidedMeditation(Request $request)
    {
        try {
            $query = Content::with(['category', 'contentType'])
                ->where('category_id', 5);

            if ($request->filled('level') && $request->level !== 'all') {
                $query->where('type', $request->level);
            }
            if ($request->filled('duration') && $request->duration !== 'all') {
                $duration = $request->duration;
                if ($duration === '5-10') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 5 AND TIME_TO_SEC(video_length)/60 <= 10");
                } elseif ($duration === '20-30') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 20 AND TIME_TO_SEC(video_length)/60 <= 30");
                } elseif ($duration === '30-40') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 30 AND TIME_TO_SEC(video_length)/60 <= 40");
                } elseif ($duration === '40-60') {
                    $query->whereRaw("TIME_TO_SEC(video_length)/60 >= 40 AND TIME_TO_SEC(video_length)/60 <= 60");
                }
            }
            if ($request->filled('type') && $request->type !== 'all') {
                $query->where('content_type_id', $request->type);
            }
            $results = $query->latest()->get();

            if ($results->isEmpty()) {
                return $this->success([
                    'category' => 'Guided Meditation',
                    'contents' => []
                ], 'No content found.', 200);
            }

            $formatted = [
                'category' => $results->first()->category->title ?? 'Guided Meditation',
                'contents' => $results->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'title'         => $item->title,
                        'description'   => $item->description,
                        'image'         => $item->image ? url($item->image) : null,
                        'video'         => $item->video ? url($item->video) : null,
                        'video_duration'=> $item->video_length ?? null,
                        'level'         => $item->type,
                        'type'          => $item->contentType->title ?? null,
                    ];
                })->values(),
            ];

            return $this->success($formatted, 'Content fetched successfully.', 200);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 422);
        }
    }
}
