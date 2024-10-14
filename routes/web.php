<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/site/{any}', function () {
    return file_get_contents(public_path('site/index.html'));
})->where('any', '.*');