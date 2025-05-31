<?php

namespace App\Http\Controllers\Web\Backend;


use Exception;
use App\Models\Category;
use App\Models\ContentType;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use ApiResponse;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($data) {
                    return $data->created_at ? $data->created_at->format('Y-m-d H:i') : '';
                })
                ->make();
        }
        return view("backend.layouts.category.index");
    }


    public function category()
    {
        try {
            $data = Category::all();

            if (!$data) {
                return $this->success([], 'No data found', 200);
            }

            return $this->success($data, 'Content Type retrieved successfully', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function contentType()
    {
        try {
            $data = ContentType::all();

            if (!$data) {
                return $this->success([], 'No data found', 200);
            }

            return $this->success($data, 'Content Type retrieved successfully', 200);
        } catch (Exception $e) {
            
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
