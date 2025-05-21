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

            if ($user->role === 'user') {
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                ];
            } else {
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'country' => $user->country,
                    'description' => $user->description,
                    'avatar' => $user->avatar,
                ];
            }



            return $this->success($userData, 'User Profile Retrived successfull', 200);
        } catch (Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'        => ['nullable', 'string', 'max:255'],
                'country'     => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 422);
            }

            $user = auth('api')->user();
            $data = $validator->validated();

            $updatableFields = ['name'];
            if ($user->role !== 'user') {
                $updatableFields = array_merge($updatableFields, ['country', 'description']);
            }

            $filteredData = collect($data)->only($updatableFields)->toArray();

            $user->update($filteredData);

            $userData = [
                'id'   => $user->id,
                'name' => $user->name,
            ];

            if ($user->role !== 'user') {
                $userData['country']     = $user->country;
                $userData['description'] = $user->description;
            }

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

    public function updateAvatar(Request $request)
    {
        try {
            
             $validator = Validator::make($request->all(), [
               'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            ]);

            if ($validator->fails()) {
                return $this->error([], $validator->errors()->first(), 200);
            }

            $user = Auth::user();

            if ($user->avatar) {

                Helper::deleteAvatar($user->avatar);
            }

            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $imagePath = Helper::uploadImage($image, 'profile');
                $user->avatar = $imagePath;
            }

            $user->save();

            $updatedUser = User::select('id', 'avatar')->find(auth('api')->id());

            return $this->success($updatedUser, 'Avatar updated successfully.', 200);
        } catch (Exception $e) {

            Log::info($e->getMessage());
            return $this->error([], 'An unexpected error occurred. Please try again.', 500);
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
