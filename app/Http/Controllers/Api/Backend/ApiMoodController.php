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

            $mood = Mood::updateOrCreate(
                ['user_id' => $userId],
                ['mood' => $request->mood]
            );

            return $this->success($mood, 'Mood updated successfully.', 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], 'Something went wrong.', 500);
        }
    }
}
