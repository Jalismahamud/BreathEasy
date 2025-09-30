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
use Illuminate\Support\Facades\File;

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


        $videos = DailyVideo::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('backend.layouts.daily_video.index', compact('videos', 'selectedMonth', 'selectedYear'));
    }

    public function createOrUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video' => 'required|file',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {

            $uploadDate = $request->input('upload_date');
            if ($uploadDate) {

                try {
                    $targetDate = Carbon::createFromFormat('Y-m-d', $uploadDate, 'UTC')->setTimeFromTimeString(Carbon::now('UTC')->format('H:i:s'));
                } catch (\Exception $e) {
                    $targetDate = Carbon::today('UTC');
                }
            } else {
                $targetDate = Carbon::today('UTC');
            }


            $existing = DailyVideo::whereDate('created_at', $targetDate)->first();
            if ($existing) {
                Helper::deleteImage($existing->video);
                $existing->delete();
            }

            if ($request->hasFile('video')) {
                $file = $request->file('video');
                $videoPath = Helper::uploadImage($file, 'daily-videos');

                $record = DailyVideo::create([
                    'video' => $videoPath,
                ]);

                $record->created_at = $targetDate;
                $record->updated_at = $targetDate;
                $record->save();

                session()->put('t-success', 'Video uploaded successfully.');
            }
        } catch (Exception $e) {
            Log::error('Daily Video Error: ' . $e->getMessage());
            session()->put('t-error', 'Something went wrong. Please try again.');
        }

        return redirect()->back();
    }

    public function chunkUpload(Request $request)
    {
    $fileName    = $request->fileName;
    $chunkIndex  = is_numeric($request->chunkIndex) ? intval($request->chunkIndex) : 0;
    $totalChunks = is_numeric($request->totalChunks) ? intval($request->totalChunks) : 1;
        $uploadDate  = $request->input('upload_date');

        $tempDir  = public_path('uploads/daily-videos/tmp');
        $tempPath = $tempDir . '/' . $fileName;

        try {
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0777, true);
            }


            if ($request->hasFile('file')) {
                $request->file('file')->move($tempDir, $fileName . ".part" . $chunkIndex);
            } else {
                Log::warning('DailyVideo chunk upload received without file', ['fileName'=>$fileName, 'chunk'=>$chunkIndex]);
                return response()->json(['status' => 'error', 'message' => 'No chunk file received'], 400);
            }


            if ($chunkIndex + 1 == $totalChunks) {
                $finalDir = public_path('uploads/daily-videos');
                if (!File::exists($finalDir)) {
                    File::makeDirectory($finalDir, 0777, true);
                }

                $finalPath = $finalDir . '/' . $fileName;


                $out = fopen($finalPath, "ab");
                for ($i = 0; $i < $totalChunks; $i++) {
                    $chunkFile = $tempDir . '/' . $fileName . ".part" . $i;
                    $in = fopen($chunkFile, "rb");
                    stream_copy_to_stream($in, $out);
                    fclose($in);
                    unlink($chunkFile);
                }
                fclose($out);


                if ($uploadDate) {
                    try {
                        $targetDate = Carbon::createFromFormat('Y-m-d', $uploadDate, 'UTC')->setTimeFromTimeString(Carbon::now('UTC')->format('H:i:s'));
                    } catch (\Exception $e) {
                        $targetDate = Carbon::today('UTC');
                    }
                } else {
                    $targetDate = Carbon::today('UTC');
                }

                $existing = DailyVideo::whereDate('created_at', $targetDate)->first();
                if ($existing) {
                    Helper::deleteImage($existing->video);
                    $existing->delete();
                }

                $record = DailyVideo::create([
                    'video' => 'uploads/daily-videos/' . $fileName,
                ]);

                $record->created_at = $targetDate;
                $record->updated_at = $targetDate;
                $record->save();

                Log::info('DailyVideo chunk upload assembled', ['file' => $finalPath]);

                return response()->json(['status' => 'ok', 'path' => 'uploads/daily-videos/' . $fileName], 200);
            }
            return response()->json(['status' => 'ok']);
        } catch (Exception $e) {
            Log::error('Daily Video Chunk Upload Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Upload failed.'], 500);
        }
    }
}
