<?php
use App\Models\DynamicPage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\UserAccountController;



Route::get('/',function (){
    return view('welcome');
})->middleware('authCheck')->name('home');

Route::get('privacy-policy', function () {
    $data = DynamicPage::where('page_slug', 'privacy-policy')->first();

    if (!$data) {
        $content = 'Privacy Policy data not found.';
    } else {
        $content = $data->page_content;
    }

    return view('privacy-policy', compact('content'));
})->name('privacy-policy');

Route::get('app/login', [UserAccountController::class, 'create'])->name('app.login');
Route::post('app/login/store', [UserAccountController::class, 'store'])->name('app.login.store');
Route::post('app/user/logout', [UserAccountController::class, 'destroy'])->name('app.user.logout');
Route::post('app/user/delete/{user}', [UserAccountController::class, 'delete_account'])->name('app.user.delete.account');


require __DIR__.'/auth.php';



