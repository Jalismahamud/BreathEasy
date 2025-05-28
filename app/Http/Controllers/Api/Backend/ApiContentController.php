<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\Content;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiContentController extends Controller
{
    use ApiResponse;

    public function hathaYoga(Request $request)
    {
        $query = Content::with(['category', 'contentType', 'contentDuration'])->where('category_id', 1);

        dd($query);


        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('title', $request->category);
            });
        }


        if ($request->has('level')) {
            $query->where('level', $request->level);
        }


        if ($request->has('min_duration') && $request->has('max_duration')) {
            $query->whereHas('contentDuration', function ($q) use ($request) {
                $q->whereBetween('length', [
                    (int) $request->min_duration,
                    (int) $request->max_duration
                ]);
            });
        }


        if ($request->has('goal')) {
            $query->whereJsonContains('goals', $request->goal);
        }

        $results = $query->latest()->get();

        return $this->success($results, 'Content fetched successfully.', 200);
    }
}
