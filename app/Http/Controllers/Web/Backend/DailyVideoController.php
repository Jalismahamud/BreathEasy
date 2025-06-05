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



    public function index()
    {
        $videos = DailyVideo::latest()->paginate(10);
        return view('backend.layouts.daily_video.index', compact('videos'));
    }

    public function createOrUpdate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'video' => 'required',
        'video.*' => 'file|mimetypes:video/mp4',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    try {
        $today = Carbon::today('UTC');
        $uploadedCount = 0;
        if ($request->hasFile('video')) {
            foreach ($request->file('video') as $file) {
                $videoPath = Helper::uploadImage($file, 'daily-videos');
                DailyVideo::create([
                    'video' => $videoPath,
                ]);
                $uploadedCount++;
            }
            session()->put('t-success', $uploadedCount . ' video(s) uploaded successfully.');
        }
    } catch (Exception $e) {
        Log::error('Daily Video Error: ' . $e->getMessage());
        session()->put('t-error', 'Something went wrong. Please try again.');
    }

    return redirect()->back();
}

}
