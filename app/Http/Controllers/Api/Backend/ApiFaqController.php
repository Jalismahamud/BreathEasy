<?php

namespace App\Http\Controllers\Api\Backend;

use App\Models\Faq;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApiFaqController extends Controller
{
    use  ApiResponse;

    public function faqs()
    {
        try {
            $faqs = Faq::where('status', 'active')->get();

            if (!$faqs) {
                return $this->success([], 'Faq not Found.', 200);
            }

        $data = $faqs->map(function ($faq) {
            return [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
            ];
        });

            return $this->success($data, 'Faqs retrieved successfully.',200);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }

}
