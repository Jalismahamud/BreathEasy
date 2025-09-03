<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use Carbon\Carbon;
use App\Helper\Helper;
use App\Models\DailyVideo;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DailyVideoController extends Controller
{
    use ApiResponse;

    public function dailyVideo()
    {
        try {
            $today = Carbon::today('UTC');

            $todayVideo = DailyVideo::whereDate('created_at', $today)->first();

            if ($todayVideo) {
                $todayVideo['video'] = url($todayVideo->video);
                return $this->success($todayVideo, 'Today\'s video found.', 200);
            } else {
                $randomVideo = Cache::remember('daily_random_video', 86400, function () {
                    return DailyVideo::inRandomOrder()->first();
                });

                if ($randomVideo) {
                    $randomVideo['video'] = url($randomVideo->video);
                    return $this->success($randomVideo, 'Random video (cached) found.', 200);
                } else {
                    return $this->error([], 'No video found in database.', 200);
                }
            }
        } catch (Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function allDailyVideos()
    {
        try {
            $data = DailyVideo::orderBy('created_at', 'desc')->get();

            if ($data->isEmpty()) {
                return $this->error([], 'No videos found.', 200);
            }

            $videos = $data->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video' => url($video->video),
                    'created_at' => $video->created_at->diffForHumans(),
                ];
            });

            return $this->success($videos, 'Videos fetched successfully.', 200);
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
