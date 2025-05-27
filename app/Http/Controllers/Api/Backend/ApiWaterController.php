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


    // public function deleteIntake($id)
    // {
    //     $entry = WaterIntake::where('id', $id)->where('user_id', auth('api')->id())->first();

    //     if (!$entry) {
    //         return $this->error([], 'Entry not found', 404);
    //     }

    //     $entry->delete();

    //     return $this->success([], 'Entry deleted successfully');
    // }



    public function deleteIntake(Request $request)
    {
        $userId = auth('api')->id();
        $today  = now('UTC')->toDateString();

        // Validate incoming amount
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|in:100,200,300',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $amount    = $request->input('amount');
        $remaining = $amount;

       
        $entries = WaterIntake::where('user_id', $userId)
            ->where('date',    $today)
            ->orderBy('created_at', 'asc')
            ->get();

        
        foreach ($entries as $entry) {
            if ($remaining <= 0) {
                break;
            }

            if ($entry->amount <= $remaining) {
               
                $remaining -= $entry->amount;
                $entry->delete();
            } else {
               
                $entry->amount -= $remaining;
                $entry->save();
                $remaining = 0;
            }
        }

        if ($remaining > 0) {
            return $this->error([],"Unable to remove the full {$amount}ml; only removed " . ($amount - $remaining) . "ml.",400);
        }

        return $this->success([], "{$amount}ml water intake deleted successfully.");
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
