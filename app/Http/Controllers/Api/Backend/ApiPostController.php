<?php

namespace App\Http\Controllers\Api\Backend;


use App\Models\Post;

use App\Helper\Helper;
use App\Models\PostImage;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ApiPostController extends Controller
{
    use ApiResponse;

    public function allPosts()
    {
        try {
            $posts = Post::with([
                'user:id,f_name,l_name,avatar',
                'images:id,image,post_id',
                'reacts:id,like,comment,post_id,user_id'
            ])->latest()->get();

            if ($posts->isEmpty()) {
                return $this->success([], 'No posts found.', 200);
            }

            $response = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'message' => $post->message,
                    'images' => $post->images->map(function ($img) {
                        return url($img->image);
                    }),
                    'created_at' => $post->created_at->diffForHumans(),
                    'like' => $post->reacts->count('like') > 0 ? $post->reacts->count('like') : 0,
                    'is_like' => $post->reacts()->where('user_id', auth('api')->id())->where('like', true)->exists(),
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->f_name . ' ' . $post->user->l_name,
                        'avatar' => $post->user->avatar,
                    ],
                ];
            });
            return $this->success($response, 'Posts retrieved successfully.', 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function myPosts()
    {
        try {
            $posts = Post::with([
                'user:id,f_name,l_name,avatar',
                'images:id,image,post_id',
                'reacts:id,like,comment,post_id,user_id'
            ])->where('user_id', auth('api')->id())->latest()->get();

            if ($posts->isEmpty()) {
                return $this->success([], 'No posts found.', 200);
            }

            $response = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'message' => $post->message,
                    'images' => $post->images->map(function ($img) {
                        return url($img->image);
                    }),
                    'created_at' => $post->created_at->diffForHumans(),
                    'like' => $post->reacts->where('like', 1)->count(),
                    'is_like' => $post->reacts()->where('user_id', auth('api')->id())->where('like', true)->exists(),
                    'user' => [
                        'id' => $post->user->id,
                        'name' => $post->user->f_name . ' ' . $post->user->l_name,
                        'avatar' => $post->user->avatar,
                    ],
                ];
            });

            return $this->success($response, 'Posts retrieved successfully.', 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function createPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $post = Post::create([
                'user_id' => auth('api')->id(),
                'message' => $request->message,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = Helper::uploadImage($image, 'posts');
                    PostImage::create([
                        'post_id' => $post->id,
                        'image' => $imagePath,
                    ]);
                }
            }

            DB::commit();
            return $this->success($post->load('images'), 'Post created successfully.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }



    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'message' => 'sometimes|required|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $post = Post::findOrFail($id);

            if ($post->user_id !== auth('api')->id()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($request->has('message')) {
                $post->message = $request->message;
                $post->save();
            }

            if ($request->hasFile('images')) {
                foreach ($post->images as $img) {

                    $img->delete();
                }
                foreach ($request->file('images') as $image) {
                    $path = $image->store('posts', 'public');
                    PostImage::create([
                        'post_id' => $post->id,
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'data' => $post->load('images'), 'message' => 'Post updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deletePost($post_id)
    {
        DB::beginTransaction();
        try {
            $post = Post::find($post_id);

            if (!$post) {
                return $this->error([], 'Post not found.', 404);
            }

            if ($post->user_id !== auth('api')->id()) {
                return $this->success([], 'Unauthorized', 403);
            }

            foreach ($post->images as $img) {
                Helper::deleteImage($img->image);
                $img->delete();
            }

            $post->delete();

            DB::commit();
            return $this->success([], 'Post deleted successfully.', 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
