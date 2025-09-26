<?php

namespace App\Http\Controllers\Web\Backend;


use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $totalUser = User::where('role', 'user')->count();
        $faq =  DB::table('faqs')->count();
        $totalCategory = DB::table('categories')->count();
        $totalContent = DB::table('contents')->count();

        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $selectedMonth =  $month = $request->input('month', now()->format('Y-m'));

         // Generate date range for the selected month

        $dates = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        $userChartData = $dates->map(function ($date) {
            return User::whereDate('created_at', $date)->count();
        });

        $monthlyJoinedUsers = User::whereBetween('created_at', [$start, $end])
            ->where('role', 'user')
            ->count();


        return view('backend.layouts.dashboard', compact(
            'totalUser',
            'faq',
            'totalCategory',
            'totalContent',
            'dates',
            'userChartData',
            'monthlyJoinedUsers',
            'selectedMonth'

        ));
    }
}
