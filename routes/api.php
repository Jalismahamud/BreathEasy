<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\UserProfileController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\AuthenticationController;
use App\Http\Controllers\Web\Backend\Settings\DynamicPageController;


Route::get('privacy-policy', [DynamicPageController::class, 'privacyPolicy']);
Route::get('terms-and-condition', [DynamicPageController::class, 'termsAndConditions']);


Route::group(['middleware' => 'guest:api', ], function () {
    Route::post('/verify-account', [AuthenticationController::class, 'verifyAccountId']);
    Route::post('/update-password', [UserProfileController::class, 'updatePassword']);
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/register-otp-verify', [AuthenticationController::class, 'RegistrationVerifyOtp']);
    Route::post('forgot-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/verify-otp', [ResetPasswordController::class, 'VerifyOTP']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
});



Route::group(['middleware' => ['auth:api'], 'prefix' => 'admin'], function () {

    Route::get('/profile', [UserProfileController::class, 'profile']);
    Route::post('/update-profile', [UserProfileController::class, 'updateProfile']);
    Route::post('/update-avatar', [UserProfileController::class, 'updateAvatar']);

    Route::delete('/delete-profile', [UserProfileController::class, 'deleteProfile']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);


});








