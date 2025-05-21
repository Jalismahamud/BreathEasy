<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\CMS;
use App\Models\User;
use App\Enums\SectionEnum;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        $totalUser = User::where('role', 'user')->count();
      

        return view('backend.layouts.dashboard', compact(
            'totalUser',
           
        ));
    }
}
