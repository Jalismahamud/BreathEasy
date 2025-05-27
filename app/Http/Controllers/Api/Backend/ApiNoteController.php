<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Validator;

class ApiNoteController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $notes = Note::with('user')->latest()->get();

            if ($notes->isEmpty()) {
                return $this->success([], 'No notes found.', 404);
            }

            $response = $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'date' => $note->created_at->format('M d, Y'),
                    'time' => $note->created_at->format('h:i A'),
                ];
            });

            return $this->success($response, 'Notes retrieved successfully.', 200);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->error([], 'Something went wrong.', 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $note = Note::create([
                'user_id' => auth('api')->id(),
                'note' => $request->note,
            ]);

            return $this->success($note, 'Note created successfully.', 201);
        } catch (\Exception $e) {

            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function delete($note_id)
    {
        try {
            $note = Note::find($note_id);

            if (!$note) {
                return $this->error([], 'Note not found.', 404);
            }

            if ($note->user_id !== auth('api')->id()) {
                return $this->error([], 'Unauthorized', 403);
            }

            $note->delete();

            return $this->success([], 'Note deleted successfully.', 200);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
