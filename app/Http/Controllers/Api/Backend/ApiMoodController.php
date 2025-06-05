<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\Mood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class ApiMoodController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $query = Mood::where('user_id', auth('api')->id());


            if ($request->has('month')) {
                $month = $request->input('month');
                $year = $request->input('year', now('UTC')->year); 
                $query->whereMonth('date', $month)->whereYear('date', $year);
            }

            $data = $query->orderBy('date', 'desc')->get();

            if ($data->isEmpty()) {
                return $this->error([], 'No mood data found.', 200);
            }


            $data = $data->map(function ($mood) {
                return [
                    'date' => $mood->date,
                    'mood' => $mood->mood,
                ];
            });

            return $this->success($data, 'Mood data retrieved successfully.', 200);

        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->error([], 'Something went wrong.', 500);
        }
    }



    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mood' => 'required|in:terrible,bad,okey,good,excellent',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $userId = auth('api')->id();
            $todayUtc = now('UTC')->toDateString();

            $mood = Mood::updateOrCreate(
                [
                    'user_id' => $userId,
                    'date' => $todayUtc
                ],
                [
                    'mood' => $request->mood
                ]
            );

            return $this->success($mood, 'Mood updated successfully.', 200);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->error([], 'Something went wrong.', 500);
        }
    }
}
