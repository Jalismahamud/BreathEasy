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
            $video = DailyVideo::latest()->first();

            if (!$video) {
                return $this->error([], 'Video not found.', 404);
            }

            return $this->success($video, 'Video found.', 200);
        } catch (Exception $e) {

            return $this->error([], $e->getMessage(), 500);
        }
    }



    public function index()
    {
        $videos = DailyVideo::latest()->paginate(10);
        return view('backend.layouts.daily_video.index', compact('videos'));
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
        $dailyVideo = DailyVideo::whereDate('created_at', $today)->first();

        if ($request->hasFile('video')) {
            $videoPath = Helper::uploadImage($request->file('video'), 'daily-videos');

            if ($dailyVideo) {
                if ($dailyVideo->video && file_exists(public_path($dailyVideo->video))) {
                    Helper::deleteImage($dailyVideo->video);
                }

                $dailyVideo->update([
                    'video' => $videoPath,
                ]);
                session()->put('t-success', 'Daily video updated successfully.');
            } else {
                DailyVideo::create([
                    'video' => $videoPath,
                ]);
                session()->put('t-success', 'Daily video created successfully.');
            }
        }
    } catch (Exception $e) {
        Log::error('Daily Video Error: ' . $e->getMessage());
        session()->put('t-error', 'Something went wrong. Please try again.');
    }

    return redirect()->back();
}

}
