<?php

use Illuminate\Support\Facades\Route;
use App\Models\SiteSetting;

Route::get('/', function () {
    return redirect()->route('admin.home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');