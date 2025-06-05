<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use App\Helper\Helper;
use App\Models\Content;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class GuidedMeditationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Content::with(['contentType'])
                ->where('category_id', 5)
                ->latest()->orderBy('id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('content_type', fn($data) => $data->contentType?->title ?? 'N/A')
                ->addColumn('type', fn($data) => $data->type ?? 'N/A')
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group">
                                <a href="' . route('admin.content.guided-meditations.edit', $data->id) . '" class="btn btn-primary text-white" title="Edit">
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

        return view('backend.layouts.guided-meditations.index');
    }

    public function create()
    {
        $contentTypes = ContentType::all();
        return view('backend.layouts.guided-meditations.create', compact('contentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content_type_id' => 'required|exists:content_types,id',
            'type' => 'required|in:Beginner,Intermediate,Advanced',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'audio' => 'required|file|mimes:mp3,wav,m4a',
            'audio_length' => 'nullable|string'
        ]);

        try {
            $validated['category_id'] = 5;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = Helper::uploadImage($image, 'contents');
                $validated['image'] = $imagePath;
            } else {
                $validated['image'] = null;
            }

            if ($request->hasFile('audio')) {
                $audio = $request->file('audio');
                $audioPath = Helper::uploadImage($audio, 'contents');
                $validated['video'] = $audioPath; // Store audio in 'video' column
            }

            $validated['video_length'] = $request->input('audio_length') ?? '00:00:00';

            Content::create($validated);

            session()->put('t-success', 'Guided Meditation created successfully');
        } catch (Exception $e) {
            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.content.guided-meditations.index');
    }

    public function edit($id)
    {
        $data = Content::findOrFail($id);
        $contentTypes = ContentType::all();
        return view('backend.layouts.guided-meditations.edit', compact('data', 'contentTypes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'content_type_id' => 'required|exists:content_types,id',
            'type' => 'required|in:Beginner,Intermediate,Advanced',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'audio' => 'nullable|file|mimes:mp3,wav,m4a',
            'audio_length' => 'nullable|string'
        ]);

        try {
            $content = Content::findOrFail($id);
            $validated['category_id'] = 5;

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

            if ($request->hasFile('audio')) {
                if ($content->video) {
                    Helper::deleteAvatar($content->video);
                }
                $audio = $request->file('audio');
                $audioPath = Helper::uploadImage($audio, 'contents');
                $validated['video'] = $audioPath; // Store audio in 'video' column
                $validated['video_length'] = $request->input('audio_length') ?? '00:00:00';
            }

            $content->update($validated);
            session()->put('t-success', 'Guided Meditation updated successfully');
        } catch (Exception $e) {
            session()->put('t-error', $e->getMessage());
        }

        return redirect()->route('admin.content.guided-meditations.index');
    }

    public function destroy($id): JsonResponse
    {
        $data = Content::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Guided Meditation not found.',
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
            'message' => 'Guided Meditation deleted successfully!',
        ], 200);
    }
}
