<?php
use App\Models\DynamicPage;
use Illuminate\Support\Facades\Route;



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


require __DIR__.'/auth.php';



