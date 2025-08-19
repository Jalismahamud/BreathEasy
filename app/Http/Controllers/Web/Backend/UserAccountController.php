<?php

namespace App\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserAccountController extends Controller
{

    // Show the login form
    public function create()
    {
        return view('auth.app_login');
    }

    // Handle the login request
    public function store(Request $request)
    {


        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $credentials = $request->only('email', 'password');

            if (!$token = auth('api')->attempt($credentials)) {
                return redirect()->back()->with('error', 'Invalid credentials');
            }

            $user = auth('api')->user();

            // dd($user);


            if ($user->role != 'admin') {
                $user = auth('api')->user();

                return view('backend.layouts.user.dashbaord', compact('user'));
            }
        } catch (\Exception $e) {

            Log::error($e->getMessage());

        }
    }


    // Handle the logout request
    public function destroy(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            auth()->logout();
        }
        return redirect('/')->with('success', 'Logout successful');
    }







    // user dashboard
    public function user_dashboard()
    {
        return view('backend.layouts.user.dashbaord');
    }


    // user delete account

    public function delete_account(Request $request, $user)
    {

        $user = User::find($user);

        // $user->orders()->delete();
        // $user->wishlists()->delete();
        // $user->carts()->delete();
        // $user->addresses()->delete();
        // $user->notifications()->delete();
        // $user->reviews()->delete();
        // $user->comments()->delete();
        // $user->messages()->delete();
        // $user->transactions()->delete();
        // $user->coupons()->delete();



        if (!$user) {
            return redirect('/')->with('error', 'User not found');
        }


        // Delete the user account
        $user->delete();


        return redirect('/app/login')->with('t-success', 'Account deleted successfully');
    }
}
