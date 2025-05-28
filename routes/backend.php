<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Backend\FaqController;
use App\Http\Controllers\Web\Backend\ContentController;
use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\UserListController;

use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\DailyVideoController;
use App\Http\Controllers\Web\Backend\CMS\AuthPageController;
use App\Http\Controllers\Web\Backend\Settings\ProfileController;
use App\Http\Controllers\Web\Backend\Settings\SettingController;
use App\Http\Controllers\Web\Backend\Settings\DynamicPageController;
use App\Http\Controllers\Web\Backend\Settings\MailSettingController;



Route::middleware(['auth:web', 'admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::get('/user-list', [UserListController::class, 'index'])->name('admin.user.index');
Route::delete('/user-list/delete/{id}', [UserListController::class, 'destroy'])->name('admin.user.destroy');


//! Route for Profile Settings
Route::controller(ProfileController::class)->group(function () {
    Route::get('setting/profile', 'index')->name('setting.profile.index');
    Route::put('setting/profile/update', 'UpdateProfile')->name('setting.profile.update');
    Route::put('setting/profile/update/Password', 'UpdatePassword')->name('setting.profile.update.Password');
    Route::post('setting/profile/update/Picture', 'UpdateProfilePicture')->name('update.profile.picture');
});


Route::prefix('category')->name('admin.category.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');             
    Route::post('/store', [CategoryController::class, 'store'])->name('store');      
    Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');     
    Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy'); 
    Route::post('/status/{id}', [CategoryController::class, 'status'])->name('status');     
});


Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('daily-videos', [DailyVideoController::class, 'index'])->name('admin.daily-video.index');
    Route::post('daily-videos', [DailyVideoController::class, 'createOrUpdate'])->name('admin.daily-video.createOrUpdate');
});



Route::prefix('admin/content')->name('admin.content.')->group(function () {
    Route::get('/', [ContentController::class, 'index'])->name('index');
    Route::get('/create', [ContentController::class, 'create'])->name('create');
    Route::post('/store', [ContentController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [ContentController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [ContentController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [ContentController::class, 'destroy'])->name('destroy');
});




//! Route for Mail Settings
Route::controller(MailSettingController::class)->group(function () {
    Route::get('setting/mail', 'index')->name('setting.mail.index');
    Route::patch('setting/mail', 'update')->name('setting.mail.update');
});




//! Route for Stripe Settings
Route::controller(SettingController::class)->group(function () {
    Route::get('setting/general', 'index')->name('setting.general.index');
    Route::patch('setting/general', 'update')->name('setting.general.update');
});

//Auth Page CMS
Route::controller(AuthPageController::class)->prefix('cms')->name('cms.')->group(function () {
    Route::get('page/auth/section/bg', 'index')->name('page.auth.section.bg.index');
    Route::patch('page/auth/section/bg', 'update')->name('page.auth.section.bg.update');
});

// CMS Routes

Route::prefix('cms')->name('admin.cms.')->group(function () {



   

});

Route::controller(DynamicPageController::class)->group(function () {
    Route::get('/dynamic-page', 'index')->name('admin.dynamic_page.index');
    Route::get('/dynamic-page/create', 'create')->name('admin.dynamic_page.create');
    Route::post('/dynamic-page/store', 'store')->name('admin.dynamic_page.store');
    Route::get('/dynamic-page/edit/{id}', 'edit')->name('admin.dynamic_page.edit');
    Route::put('/dynamic-page/update/{id}', 'update')->name('admin.dynamic_page.update');
    Route::post('/dynamic-page/status/{id}', 'status')->name('admin.dynamic_page.status');
    Route::delete('/dynamic-page/destroy/{id}', 'destroy')->name('admin.dynamic_page.destroy');
});



Route::controller(FaqController::class)->group(function () {
    Route::get('/faq', 'index')->name('admin.faq.index');
    Route::get('/faq/create', 'create')->name('admin.faq.create');
    Route::post('/faq', 'store')->name('admin.faq.store');
    Route::get('/faq/edit/{id}', 'edit')->name('admin.faq.edit');
    Route::put('/faq/{id}', 'update')->name('admin.faq.update');
    Route::post('/faq/status/{id}', 'status')->name('admin.faq.status');
    Route::delete('/faq/{id}', 'destroy')->name('admin.faq.destroy');
});

