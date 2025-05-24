<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Backend\ApiFaqController;
use App\Http\Controllers\Api\Backend\ApiPostController;
use App\Http\Controllers\Api\Auth\UserProfileController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\AuthenticationController;
use App\Http\Controllers\Web\Backend\Settings\DynamicPageController;


Route::get('/faqs', [ApiFaqController::class, 'faqs']);
Route::get('privacy-policy', [DynamicPageController::class, 'privacyPolicy']);
Route::get('terms-and-condition', [DynamicPageController::class, 'termsAndConditions']);


Route::group(['middleware' => 'guest:api', ], function () {
   
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/register-otp-verify', [AuthenticationController::class, 'registrationVerifyOtp']);
    Route::post('/forgot-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/resend-otp', [ResetPasswordController::class, 'resendCode']);
    Route::post('/verify-otp', [ResetPasswordController::class, 'VerifyOTP']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
});



Route::group(['middleware' => ['auth:api']], function () {

    Route::get('/profile', [UserProfileController::class, 'profile']);
    Route::post('/update-profile', [UserProfileController::class, 'updateProfile']);
    Route::post('/update-avatar', [UserProfileController::class, 'updateAvatar']);
     Route::post('/update-password', [UserProfileController::class, 'updatePassword']);

    Route::delete('/delete-profile', [UserProfileController::class, 'deleteProfile']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);
    
    
    
    Route::get('/posts',[ApiPostController::class, 'allPosts']);
    Route::post('/post/create',[ApiPostController::class, 'createPost']);
    Route::delete('/post/delete/{post_id}',[ApiPostController::class, 'deletePost']);
    Route::post('/post/like/{post_id}',[ApiPostController::class, 'likePost']);


});








