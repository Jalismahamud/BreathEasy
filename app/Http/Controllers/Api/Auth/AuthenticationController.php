<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendOtpMail;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Cache;


class AuthenticationController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'user_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $validatedData = $validator->validated();

            $otp = rand(1000, 9999);
            $otpExpiresAt = now()->addMinutes(5);

            $email = $validatedData['email'];

            $cacheData = array_merge($validatedData, [
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
            ]);

            Cache::put("register_otp_{$email}", $otp, 300);
            Cache::put("register_data_{$email}", $cacheData, 300);

            // Send mail
            Mail::to($email)->send(new SendOtpMail($otp, (object)$validatedData));

            return $this->success(
                [
                    'message' => 'OTP has been sent to your email. Please verify to complete registration.',
                    'name' => $validatedData['name'],
                    'user_name' => $validatedData['user_name'],
                    'email' => $email,
                    'otp' => $otp,
                ],
                'OTP Sent successfully.',
                201
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], 'Something went wrong: ' . $e->getMessage(), 500);
        }
    }

    public function registrationVerifyOtp(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:4'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $email = $request->email;
        $otp = $request->otp;

        $cachedOtp = Cache::get("register_otp_{$email}");
        $cachedData = Cache::get("register_data_{$email}");

        if (!$cachedOtp || !$cachedData) {
            return $this->error([], 'OTP has expired or registration data not found.', 410);
        }

        if ($otp != $cachedOtp) {
            return $this->error([], 'Your OTP is invalid.', 403);
        }

        if (now()->gt($cachedData['otp_expires_at'])) {
            return $this->error([], 'OTP has expired.', 410);
        }

        if (User::where('email', $email)->exists()) {
            return $this->error([], 'Email already registered.', 409);
        }

        try {
            $user = User::create([
                'name' => $cachedData['name'],
                'user_name' => $cachedData['user_name'],
                'email' => $cachedData['email'],
                'password' => Hash::make($cachedData['password']),
                'is_otp_verified' => true,
                'email_verified_at' => now(),
                'role' => 'user',
            ]);

            $token = auth('api')->login($user);

            Cache::forget("register_otp_{$email}");
            Cache::forget("register_data_{$email}");

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'role' => $user->role,
                'is_otp_verified' => $user->is_otp_verified,
                'token' => $token,
            ];

            return $this->success($userData, 'Otp verified successfully. You are now registered.', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], 'Something went wrong: ' . $e->getMessage(), 500);
        }
    }


    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $credentials = $validator->validated();

            $user = User::where('email', $credentials['email'])->first();
            if (!$user) {
                return $this->error([], 'Email is incorrect or not found in our database.', 404);
            }

            if (!Hash::check($credentials['password'], $user->password)) {
                return $this->error([], 'Password is incorrect.', 401);
            }

            if (!$token = auth('api')->attempt($credentials)) {
                return $this->error([], 'Invalid email or password.', 401);
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

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->error([], 'User not found', 404);
            }

            $otp = rand(10000, 99999);
            $otpExpiresAt = now()->addMinutes(5);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => $otpExpiresAt,
            ]);

            // You can send the OTP via email or SMS here. Example:
            Mail::to($user->email)->send(new SendOtpMail($otp, $user));

            return $this->success([], 'OTP resent successfully.', 200);
        } catch (Exception $e) {

            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
