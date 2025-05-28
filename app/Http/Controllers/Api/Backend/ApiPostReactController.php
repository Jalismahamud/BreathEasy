<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\Post;
use App\Models\PostReact;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApiPostReactController extends Controller
{
    use ApiResponse;

    public function allComments()
    {
        $comments = PostReact::with(['replies', 'user'])
            ->whereNull('parent_comment_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'post_id' => $comment->post_id,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ],
                    'replies' => $comment->replies->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'comment' => $reply->comment,
                            'created_at' => $reply->created_at,
                            'user' => [
                                'id' => $reply->user->id,
                                'name' => $reply->user->name,
                            ],
                        ];
                    }),
                ];
            });

        return $this->success($comments, 'All comments with replies fetched successfully.');
    }



    public function createComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'exists:posts,id'],
            'comment' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $react = PostReact::create([
                'post_id' => $request->post_id,
                'user_id' => auth('api')->id(),
                'comment' => $request->comment,
                'like' => 0,
            ]);
            DB::commit();
            return $this->success($react, 'Comment added successfully.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function replyComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'exists:posts,id'],
            'parent_comment_id' => ['required', 'exists:post_reacts,id'],
            'comment' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $react = PostReact::create([
                'post_id' => $request->post_id,
                'user_id' => auth('api')->id(),
                'comment' => $request->comment,
                'like' => 0,
                'parent_comment_id' => $request->parent_comment_id,
            ]);
            DB::commit();
            return $this->success($react, 'Reply added successfully.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function toggleLike(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => ['required', 'exists:posts,id'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $react = PostReact::where('post_id', $request->post_id)
                ->where('user_id', auth('api')->id())
                ->first();

            if ($react) {
                $react->like = $react->like ? 0 : 1;
                $react->save();
                $message = $react->like ? 'Post liked.' : 'Like removed.';
            } else {
                $react = PostReact::create([
                    'post_id' => $request->post_id,
                    'user_id' => auth('api')->id(),
                    'like' => 1,
                ]);
                $message = 'Post liked.';
            }
            DB::commit();
            $response = [
                'post_id' => $react->post_id,
                'like' => $react->like,
            ];
            return $this->success($response, $message, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
