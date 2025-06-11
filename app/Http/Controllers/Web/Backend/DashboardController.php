<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {

        $totalUser = User::where('role', 'user')->count();
        $faq =  DB::table('faqs')->count();
        $totalCategory = DB::table('categories')->count();
        $totalContent = DB::table('contents')->count();


        return view('backend.layouts.dashboard', compact(
            'totalUser',
            'faq',
            'totalCategory',
            'totalContent'

        ));
    }
}
