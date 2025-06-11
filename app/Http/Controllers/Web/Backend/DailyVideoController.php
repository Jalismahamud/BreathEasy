<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use Carbon\Carbon;
use App\Helper\Helper;
use App\Models\DailyVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class DailyVideoController extends Controller
{
    use ApiResponse;

    public function dailyVideo()
    {
        try {
            $today = Carbon::today('UTC');
            $videos = DailyVideo::whereDate('created_at', $today)->get();
            if ($videos->isEmpty()) {
                return $this->error([], 'Video not found.', 200);
            }
            $video = $videos->random();
            $video['video'] = url($video->video);
            return $this->success($video, 'Video found.', 200);
        } catch (Exception $e) {

            return $this->error([], $e->getMessage(), 500);
        }
    }



    public function index(Request $request)
    {
        $today = Carbon::today('UTC');
        $selectedMonth = (int) $request->query('month', $today->month);
        $selectedYear = (int) $request->query('year', $today->year);

        // Fetch all videos for the selected month and year
        $videos = DailyVideo::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('backend.layouts.daily_video.index', compact('videos', 'selectedMonth', 'selectedYear'));
    }

    public function createOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file|mimetypes:video/mp4',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $today = Carbon::today('UTC');
            // Delete any existing video for today
            $existing = DailyVideo::whereDate('created_at', $today)->first();
            if ($existing) {
                Helper::deleteImage($existing->video);
                $existing->delete();
            }
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $videoPath = Helper::uploadImage($file, 'daily-videos');
                DailyVideo::create([
                    'video' => $videoPath,
                ]);
                session()->put('t-success', 'Video uploaded successfully.');
            }
        } catch (Exception $e) {
            Log::error('Daily Video Error: ' . $e->getMessage());
            session()->put('t-error', 'Something went wrong. Please try again.');
        }

        return redirect()->back();
    }
}
