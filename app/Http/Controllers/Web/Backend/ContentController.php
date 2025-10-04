<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use App\Helper\Helper;
use App\Models\Content;
use App\Models\Category;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Content::with(['category', 'contentType'])->latest()->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('category', fn($data) => $data->category?->title ?? 'N/A')
                ->addColumn('type', fn($data) => $data->type ?? 'N/A')
                ->addColumn('content_type', fn($data) => $data->contentType?->title ?? 'N/A')
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . route('admin.content.edit', $data->id) . '" class="btn btn-primary text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger text-white" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.layouts.content.index');
    }

    public function create()
    {
        $categories = Category::all();
        $contentTypes = ContentType::all();
        return view('backend.layouts.content.create', compact('categories', 'contentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'content_type_id' => 'required|exists:content_types,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file',
            'video' => 'required_without:video_path|file',
            'video_path' => 'required_without:video|string',
            'video_length' => 'nullable|string'
        ]);

        try {

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = Helper::uploadImage($image, 'contents');
                $validated['image'] = $imagePath;
            } else {
                $validated['image'] = null;
            }

            if ($request->input('video_path')) {
                $validated['video'] = $request->input('video_path');
            } elseif ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoPath = Helper::uploadImage($video, 'contents');
                $validated['video'] = $videoPath;
            }


            $validated['video_length'] = $request->input('video_length') ?? '00:00:00';

            Content::create($validated);

            session()->put('t-success', 'Content created successfully');
        } catch (Exception $e) {
            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.content.index');
    }


    public function edit($id)
    {
        $data = Content::findOrFail($id);
        $categories = Category::all();
        $contentTypes = ContentType::all();
        return view('backend.layouts.content.edit', compact('data', 'categories', 'contentTypes'));
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'content_type_id' => 'required|exists:content_types,id',
            'type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file',
            'video' => 'nullable|file',
            'video_path' => 'nullable|string',
            'video_length' => 'nullable|string'
        ]);

        try {
            $content = Content::findOrFail($id);

            if ($request->hasFile('image')) {
                if ($content->image) {
                    Helper::deleteAvatar($content->image);
                }
                $image = $request->file('image');
                $imagePath = Helper::uploadImage($image, 'contents');
                $validated['image'] = $imagePath;
            } else {
                $validated['image'] = $content->image;
            }

            if ($request->input('video_path')) {
                if ($content->video) {
                    // try deleting using Helper first
                    $deleted = Helper::deleteAvatar($content->video);
                    if (! $deleted) {
                        // try normalizing path and unlink directly
                        $rel = ltrim($content->video, '/');
                        $publicPath = public_path($rel);
                        if (file_exists($publicPath)) {
                            @unlink($publicPath);
                            \Illuminate\Support\Facades\Log::info('Content update: deleted old video by public_path', ['path'=>$publicPath]);
                        } else {
                            $storagePath = storage_path('app/public/' . $rel);
                            if (file_exists($storagePath)) {
                                @unlink($storagePath);
                                \Illuminate\Support\Facades\Log::info('Content update: deleted old video by storage_path', ['path'=>$storagePath]);
                            } else {
                                \Illuminate\Support\Facades\Log::warning('Content update: failed to delete old video', ['video'=>$content->video]);
                            }
                        }
                    }
                }
                $validated['video'] = $request->input('video_path');
                $validated['video_length'] = $request->input('video_length') ?? '00:00:00';
            } elseif ($request->hasFile('video')) {

                if ($content->video) {
                    Helper::deleteAvatar($content->video);
                }
                $video = $request->file('video');
                $videoPath = Helper::uploadImage($video, 'contents');
                $validated['video'] = $videoPath;
                $validated['video_length'] = $request->input('video_length') ?? '00:00:00';
            } else {
                $validated['video'] = $content->video;
            }



            $content->update($validated);
            session()->put('t-success', 'Content updated successfully');
        } catch (Exception $e) {
            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.content.index');
    }


    public function destroy($id): JsonResponse
    {
        $data = Content::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Content not found.',
            ], 404);
        }

        if ($data->image) {
            Helper::deleteAvatar($data->image);
        }

        if ($data->video) {
            Helper::deleteAvatar($data->video);
        }

        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content deleted successfully!',
        ], 200);
    }


    public function chunkUpload(Request $request)
    {
    $fileName    = $request->fileName;
    $chunkIndex  = is_numeric($request->chunkIndex) ? intval($request->chunkIndex) : 0;
    $totalChunks = is_numeric($request->totalChunks) ? intval($request->totalChunks) : 1;

        $tempDir  = public_path('uploads/contents/tmp');
        $tempPath = $tempDir . '/' . $fileName;

        try {
            if (!\Illuminate\Support\Facades\File::exists($tempDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($tempDir, 0777, true);
            }

            // move current chunk
            if ($request->hasFile('file')) {
                $request->file('file')->move($tempDir, $fileName . ".part" . $chunkIndex);
            } else {
                \Illuminate\Support\Facades\Log::warning('Content chunk upload received without file', ['fileName'=>$fileName, 'chunk'=>$chunkIndex]);
                return response()->json(['status' => 'error', 'message' => 'No chunk file received'], 400);
            }

            if ($chunkIndex + 1 == $totalChunks) {
                $finalDir = public_path('uploads/contents');
                if (!\Illuminate\Support\Facades\File::exists($finalDir)) {
                    \Illuminate\Support\Facades\File::makeDirectory($finalDir, 0777, true);
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

                \Illuminate\Support\Facades\Log::info('Content chunk upload assembled', ['file' => $finalPath]);
                return response()->json(['status' => 'ok', 'path' => 'uploads/contents/' . $fileName], 200);
            }

            return response()->json(['status' => 'ok']);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Content Chunk Upload Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Upload failed.'], 500);
        }
    }
}
