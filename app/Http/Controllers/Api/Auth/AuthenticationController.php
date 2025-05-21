<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class AuthenticationController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'unique:users,email'],
                'phone' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $validatedData = $validator->validated();

            $user = User::create([
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'user',
            ]);

            $token = auth('api')->login($user);

            $userData = [
                'id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'token' => $token,
            ];

            return $this->success($userData, 'User registered successfully.', 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'exists:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 200);
            }

            $credentials = $validator->validated();

            if (!$token = auth('api')->attempt($credentials)) {
                return $this->error([], 'Invalid email or password.', 200);
            }

            $user = auth('api')->user();

            $userData = [
                'id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'token' => $token,
            ];

            return $this->success($userData, 'Successfully Logged In', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function logout()
    {
        try {
            if (Auth::check('api')) {
                Auth::logout('api');
                return $this->success('Successfully loged out.', 200);
            } else {
                return $this->error([false], 'User not Authenticated.', 401);
            }
        } catch (Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

}
