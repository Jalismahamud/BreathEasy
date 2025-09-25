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
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'video' => 'required|file',
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



            if ($request->hasFile('video')) {
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
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'video' => 'nullable|file',
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


            if ($request->hasFile('video')) {

                if ($content->video) {
                    Helper::deleteAvatar($content->video);
                }

                $video = $request->file('video');
                $videoPath = Helper::uploadImage($video, 'contents');
                $validated['video'] = $videoPath;


                $validated['video_length'] = $request->input('video_length') ?? '00:00:00';
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
}
