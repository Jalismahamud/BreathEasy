<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\UserVideoActivity;
use App\Models\Content;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApiUserVideoActivityController extends Controller
{
    use ApiResponse;

       public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return $this->error([], 'Unauthorized', 401);
            }
            $query = UserVideoActivity::with(['content.category', 'content.contentType'])->where('user_id', $user->id);


            if ($request->has('date')) {
                $date = $request->input('date');
                $query->whereDate('created_at', $date);
            }

            $activities = $query->get()->map(function ($activity) {
                $content = $activity->content;
                return [
                    'id' => $activity->id,
                    'content_id' => $activity->content_id,
                    'watched_minutes' => function() use ($activity) {
                        $minutes = floor($activity->watched_seconds / 60);
                        $seconds = $activity->watched_seconds % 60;
                        if ($minutes > 0 && $seconds === 0) {
                            return $minutes . ' min';
                        } elseif ($minutes > 0) {
                            return $minutes . ' min ' . sprintf('%02d sec', $seconds);
                        } else {
                            return $seconds . ' sec';
                        }
                    },
                    'progress' => number_format(min(100, $activity->progress * 100), 2),
                    'completed' => $activity->completed ? true : false,
                    'image' => ($content && $content->image) ? url($content->image) : null,
                    'title' => $content ? $content->title : null,
                    'category' => $content && $content->category ? $content->category->title : null,
                    'content_type' => $content && $content->type ? $content->type : null,
                ];
            });
            return $this->success($activities, 'User activities fetched', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'content_id' => 'required|exists:contents,id',
                'watched_seconds' => 'required|integer|min:0',
            ]);
            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }
            $user = auth('api')->user();
            if (!$user) {
                return $this->error([], 'Unauthorized', 401);
            }
            $content = Content::find($request->content_id);
            $totalSeconds = 0;
            if ($content && $content->video_length) {
                $parts = explode(':', $content->video_length);
                if (count($parts) === 3) {
                    $totalSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                }
            }
            $watched = $request->watched_seconds;
            $progress = $totalSeconds > 0 ? min(100, round(($watched / $totalSeconds) * 100, 2)) : 0;
            $completed = $totalSeconds > 0 && $watched >= ($totalSeconds * 0.95);
            $activity = UserVideoActivity::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'content_id' => $request->content_id,
                ],
                [
                    'watched_seconds' => $watched,
                    'progress' => $progress,
                    'completed' => $completed,
                ]
            );
            return $this->success($activity, 'Activity updated', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


}
