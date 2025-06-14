<?php


use App\Models\DailyVideo;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Backend\ApiFaqController;
use App\Http\Controllers\Api\Backend\ApiMoodController;
use App\Http\Controllers\Api\Backend\ApiNoteController;
use App\Http\Controllers\Api\Backend\ApiPostController;
use App\Http\Controllers\Api\Auth\UserProfileController;
use App\Http\Controllers\Api\Backend\ApiWaterController;
use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Api\Backend\ApiReportController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Backend\ApiContentController;
use App\Http\Controllers\Web\Backend\DailyVideoController;
use App\Http\Controllers\Api\Auth\AuthenticationController;
use App\Http\Controllers\Api\Backend\ApiPostReactController;
use App\Http\Controllers\Api\Backend\ApiUserVideoActivityController;
use App\Http\Controllers\Web\Backend\Settings\DynamicPageController;

Route::get('/faqs', [ApiFaqController::class, 'faqs']);
Route::get('privacy-policy', [DynamicPageController::class, 'privacyPolicy']);
Route::get('terms-and-condition', [DynamicPageController::class, 'termsAndConditions']);
Route::get('/category',[CategoryController::class, 'category']);
Route::get('/content-type',[CategoryController::class, 'contentType']);


Route::group(['middleware' => 'guest:api',], function () {

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



    Route::get('/posts', [ApiPostController::class, 'allPosts']);
    Route::get('/my-posts', [ApiPostController::class, 'myPosts']);
    Route::post('/post/create', [ApiPostController::class, 'createPost']);
    Route::delete('/post/delete/{post_id}', [ApiPostController::class, 'deletePost']);
    Route::post('/post/like/{post_id}', [ApiPostController::class, 'likePost']);

    Route::get('/comments/{postId}', [ApiPostReactController::class, 'allComments']);
    Route::post('/post/comment', [ApiPostReactController::class, 'createComment']);
    Route::post('/post/comment/reply', [ApiPostReactController::class, 'replyComment']);
    Route::post('/post/react', [ApiPostReactController::class, 'toggleLike']);
    Route::post('/comment/react', [ApiPostReactController::class, 'toggleCommentLike']);


    Route::get('/my-notes', [ApiNoteController::class, 'index']);
    Route::post('/note/create/{contentId}', [ApiNoteController::class, 'store']);
    Route::delete('/note/delete/{note_id}', [ApiNoteController::class, 'delete']);


    Route::get('/mood', [ApiMoodController::class, 'index']);
    Route::post('/mood/create', [ApiMoodController::class, 'storeOrUpdate']);




    Route::get('/water', [ApiWaterController::class, 'index']);
    Route::post('/water/add', [ApiWaterController::class, 'addIntake']);
    Route::post('/water/delete', [ApiWaterController::class, 'deleteIntake']);
    Route::post('/water/goal', [ApiWaterController::class, 'setGoal']);


    Route::get('/user-info', [ApiContentController::class, 'userInfo']);
    Route::get('/search', [ApiContentController::class, 'search']);
    Route::get('/daily-video',[DailyVideoController::class,'dailyVideo']);


    Route::get('/hatha-yoga',[ApiContentController::class,'hathaYoga']);
    Route::get('/vinyasa-yoga',[ApiContentController::class,'vinyasaYoga']);
    Route::get('/restorative -yoga',[ApiContentController::class,'restorativeYoga']);
    Route::get('/yogic-bits',[ApiContentController::class,'yogicBits']);
    Route::get('/yogic-bits/details/{id}',[ApiContentController::class,'yogicBitsDetails']);
    Route::get('/guided-meditation',[ApiContentController::class,'guidedMeditation']);

    Route::get('latest/guided-meditation',[ApiContentController::class,'latestGuidedMeditation']);





    Route::get('user-video-activity', [ApiUserVideoActivityController::class, 'index']);
    Route::post('user-video-activity/store', [ApiUserVideoActivityController::class, 'store']);

    Route::get('/overall-activity' , [ApiReportController::class, 'overallActivity']);
    Route::get('/overall-statistics' , [ApiReportController::class, 'overallStatistics']);



});
