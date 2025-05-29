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
            $query = Content::with(['category', 'contentType', 'contentDuration'])
                ->where('category_id', 1);

            if ($request->has('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('title', $request->category);
                });
            }

            if ($request->has('level')) {
                $query->where('level', $request->level);
            }

            if ($request->has('duration')) {
                $query->whereHas('contentDuration', function ($q) use ($request) {
                    $q->whereBetween('length', [
                        (int) $request->duration,
                    ]);
                });
            }

            if ($request->has('goal')) {
                $query->whereJsonContains('goals', $request->goal);
            }

            $results = $query->latest()->get();


            if ($results->isEmpty()) {
                return $this->success([
                    'category' => $request->category ?? 'Hatha Yoga',
                    'contents' => []
                ], 'No content found.', 200);
            }


            $formatted = [
                'category' => $results->first()->category->title ?? 'Hatha Yoga',
                'contents' => $results->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'video' => $item->video_url ?? null,
                        'video_duration' => $item->contentDuration->length ?? null,
                        'type' => $item->contentType->name ?? null
                    ];
                }),
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
            $query = Content::with(['category', 'contentType', 'contentDuration'])
                ->where('category_id', 1);

            if ($request->has('category')) {
                $query->whereHas('category', function ($q) use ($request) {
                    $q->where('title', $request->category);
                });
            }

            if ($request->has('level')) {
                $query->where('level', $request->level);
            }

            if ($request->has('duration')) {
                $query->whereHas('contentDuration', function ($q) use ($request) {
                    $q->whereBetween('length', [
                        (int) $request->duration,
                    ]);
                });
            }

            if ($request->has('goal')) {
                $query->whereJsonContains('goals', $request->goal);
            }

            $results = $query->latest()->get();


            if ($results->isEmpty()) {
                return $this->success([
                    'category' => $request->category ?? 'Hatha Yoga',
                    'contents' => []
                ], 'No content found.', 200);
            }


            $formatted = [
                'category' => $results->first()->category->title ?? 'Hatha Yoga',
                'contents' => $results->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'video' => $item->video_url ?? null,
                        'video_duration' => $item->contentDuration->length ?? null,
                        'type' => $item->contentType->name ?? null
                    ];
                }),
            ];

            return $this->success($formatted, 'Content fetched successfully.', 200);
        } catch (Exception $e) {

            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 422);
        }
    }
}
