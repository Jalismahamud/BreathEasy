<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
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
                'account_id' => ['required', 'string', 'exists:users,account_id'],
                'email' => ['required', 'string', 'email'],
                'password'   => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $validatedData = $validator->validated();
            $user = User::where('account_id', $validatedData['account_id'])->first();



            $user->update([

                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),

            ]);

            $token = auth('api')->login($user);

            $userData = [
                'id' => $user->id,
                'account_id' => $user->account_id,
                'email' => $user->email,
                'role' => $user->role,

                'token' => $token,
            ];


            return $this->success($userData, 'User registered successfully.', 201);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }



    public function RegistrationVerifyOtp(Request $request)
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
            'name' => $user['f_name'] . ' ' . $user['l_name'],
            'email' => $user['email'],
            'address' => $user['address'],
            'avatar' => $user->avatar,
            'role' => $user['role'] ?? null,
            'created_at' => Carbon::parse($user['created_at'])->format('Y-m-d H:i:s'),
            'token' => $token,
        ];

        return $this->success($userData, 'Registration successful.', 200);
    }


    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_id' => ['required', 'string', 'exists:users,account_id'],
                'password'   => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 200);
            }

            $data = $validator->validated();

            //

            if (! $token = auth('api')->attempt($data)) {
                return $this->error([], 'Invalid email or password.', 200);
            }

            $credentials = $request->only('account_id', 'password');

            if (!$token = auth('api')->attempt($credentials)) {
                return $this->error('Unauthorized', 'Invalid Account ID or Password.', 200);
            }

            $user = auth('api')->user();

            $userData = [
                'id' => $user->id,
                'account_id' => $user->account_id,
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

    public function verifyAccountId(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'account_id' => ['required', 'string', 'exists:users,account_id'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $user = User::where('account_id', $request->account_id)->first();

            if (!$user) {
                return $this->error([], 'Account not found.', 404);
            }

            $isFirstTime = !$user->password;

            return $this->success([
                'account_id' => $user->account_id,
                'is_firsttime' => $isFirstTime,
            ], 'Account verification successful.', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
