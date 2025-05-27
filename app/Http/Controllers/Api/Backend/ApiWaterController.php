<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\WaterIntake;
use App\Models\WaterGoal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;


class ApiWaterController extends Controller
{
    use ApiResponse;

   
    public function index()
    {
        $userId = auth('api')->id();
        $today = now('UTC')->toDateString();


        $goal = WaterGoal::firstOrCreate(
            ['user_id' => $userId],
            ['goal' => 2500]
        );

        $intakes = WaterIntake::where('user_id', $userId)->where('date', $today)->get();

        $total = $intakes->sum('amount');

        $progress = [
            'goal' => $goal->goal,
            'intake' => $total,
            'percent' => round(($total / $goal->goal) * 100),
            //'percent' => min(100, round(($total / $goal->goal) * 100)),
            'entries' => $intakes->map(function ($i) {
                return [
                    'id' => $i->id,
                    'amount' => $i->amount,
                    'time' => $i->created_at->format('h:i A')
                ];
            })
        ];

        return $this->success($progress, 'Daily water intake fetched');
    }

    
    public function addIntake(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $entry = WaterIntake::create([
            'user_id' => auth('api')->id(),
            'amount' => $request->amount,
            'date' => now('UTC')->toDateString()
        ]);

        return $this->success($entry, 'Water intake added', 201);
    }


    public function deleteIntake($id)
    {
        $entry = WaterIntake::where('id', $id)->where('user_id', auth('api')->id())->first();

        if (!$entry) {
            return $this->error([], 'Entry not found', 404);
        }

        $entry->delete();

        return $this->success([], 'Entry deleted successfully');
    }



    public function setGoal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'goal' => 'required|integer|min:1000|max:10000'
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $goal = WaterGoal::updateOrCreate(
            ['user_id' => auth('api')->id()],
            ['goal' => $request->goal]
        );

        return $this->success($goal, 'Daily water goal updated');
    }
}
