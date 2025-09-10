<?php

namespace App\Http\Controllers\Api\Auth;

use Exception;
use App\Models\User;
use App\Helper\Helper;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    use ApiResponse;


    public function profile()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return $this->error([], 'User not found.', 200);
            }

            $userData = [
                'id' => $user->id,
                'f_name' => $user->f_name,
                'l_name' => $user->l_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'role' => $user->role,
                'status' => $user->status,
                'is_subscribed' => $user->is_subscribed,
                'package' => $user->package,
                'product_id' => $user->product_id,
                'revenuecat_id' => $user->revenuecat_id,
            ];

            return $this->success($userData, 'User Profile Retrieved successfully', 200);
        } catch (Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'f_name' => ['nullable', 'string', 'max:255'],
                'l_name' => ['nullable', 'string', 'max:255'],
                'phone'  => ['nullable', 'string', 'max:255'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $user = auth('api')->user();
            $data = $validator->validated();


            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Helper::deleteAvatar($user->avatar);
                }
                $image = $request->file('avatar');
                $imagePath = Helper::uploadImage($image, 'profile');
                $data['avatar'] = $imagePath;
            }

            $user->update($data);

            $userData = [
                'id' => $user->id,
                'f_name' => $user->f_name,
                'l_name' => $user->l_name,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
            ];

            return $this->success($userData, 'Profile updated successfully.', 200);
        } catch (Exception $e) {
            Log::error('Profile Update Error: ' . $e->getMessage());
            return $this->error([], 'Something went wrong.', 500);
        }
    }


    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password'         => ['required', 'string', 'min:8'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 200);
            }

            $user = auth('api')->user();

            $user->update(['password' => Hash::make($request->password)]);

            return $this->success(['Password updated successfully'], 'Password updated successfully.', 200);
        } catch (Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function deleteProfile()
    {
        try {
            $user = auth('api')->user();

            if ($user->avatar) {
                Helper::deleteAvatar($user->avatar);
            }

            $user->delete();

            return $this->success([], 'Profile deleted successfully.', 200);
        } catch (Exception $e) {

            Log::info($e->getMessage());
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
