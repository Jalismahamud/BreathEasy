<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
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
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $validatedData = $validator->validated();

            $otp = rand(10000, 99999);
            $otpExpiresAt = now()->addMinutes(5);

            $user = User::create([
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'user',
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
            ]);

            // You can send the OTP via email or SMS here. Example:
            // Mail::to($user->email)->send(new SendOtpMail($otp));

            $userData = [
                'id' => $user->id,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'otp' => $otp, 
            ];

            return $this->success($userData, 'User registered successfully. Please verify OTP.', 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }

     public function registrationVerifyOtp(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'digits:5'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        if ($user->otp !== $request->otp) {
            return $this->error([], 'Your OTP is Invalid.', 409);
        }

        if (Carbon::now()->gt($user->otp_expires_at)) {
            return $this->error([], 'OTP has expired', 410);
        }


        $user->update([
            'email_verified_at' => Carbon::now(),
            'is_otp_verified' => true,
            'otp' => null,
            'otp_expires_at' => null,
        ]);


        $token = auth('api')->login($user);

        $userData = [

            'id' => $user['id'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'] ?? null,
            'token' => $token,
        ];

        return $this->success($userData, 'Registration successful.', 200);
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
