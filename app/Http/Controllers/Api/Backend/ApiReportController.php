<?php

namespace App\Http\Controllers\Api\Backend;

use App\Traits\ApiResponse;
use App\Models\UserVideoActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ApiReportController extends Controller
{
    use ApiResponse;

    public function overallActivity()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $totalActivities = UserVideoActivity::where('user_id', $user->id)->where('completed', true)->count();
            $totalWatchedSeconds = UserVideoActivity::where('user_id', $user->id)->sum('watched_seconds');
            $totalWatchedMinutes = round($totalWatchedSeconds / 60, 2);
            $totalPoseLearn = DB::table('pose_learns')->where('user_id', $user->id)->count();

            return $this->success([
                'total_yoga' => $totalActivities,
                'total_watched_minutes' => $totalWatchedMinutes,
                'total_pose_learn' => $totalPoseLearn,
            ], 'Overall activity fetched successfully', 200);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }



    public function overallStatistics(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error([], 'Unauthorized', 401);
            }

            // Only week, month, year filtering
            $week = (int) $request->input('week', 1);
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);
            $firstOfMonth = \Carbon\Carbon::create($year, $month, 1);
            $startOfWeek = $firstOfMonth->copy()->addWeeks($week - 1)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->addDays(6); // Always 7 days

            $days = [];
            for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
                $day = $date->toDateString();
                // Only include days that are in the selected month
                if ($date->month != $month) {
                    continue;
                }
                // Yoga: completed activities count
                $yoga = \App\Models\UserVideoActivity::where('user_id', $user->id)
                    ->where('completed', true)
                    ->whereDate('created_at', $day)
                    ->count();

                // Minutes: sum of watched_seconds (to minutes)
                $minutes = \App\Models\UserVideoActivity::where('user_id', $user->id)
                    ->whereDate('created_at', $day)
                    ->sum('watched_seconds');
                $minutes = round($minutes / 60, 2);

                // Water: sum of water intake
                $water = \App\Models\WaterIntake::where('user_id', $user->id)
                    ->whereDate('date', $day)
                    ->sum('amount');

                // New pose: count of new poses learned
                $newPose = DB::table('pose_learns')
                    ->where('user_id', $user->id)
                    ->whereDate('viewed_at', $day)
                    ->count();

                $days[] = [
                    'date' => $day,
                    'yoga' => $yoga,
                    'minutes' => $minutes,
                    'water' => $water,
                    'new_pose' => $newPose,
                ];
            }

            return $this->success([
                'week' => [
                    'start' => $startOfWeek->toDateString(),
                    'end' => $endOfWeek->toDateString(),
                    'days' => $days,
                ]
            ], 'Weekly statistics fetched successfully', 200);

        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
