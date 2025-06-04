<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;

// Home page (landing page)
Route::get('/', function () {
    return view('landing'); // This will use the landing.blade.php view
});

// Redirect /home to / 
Route::redirect('/home', '/');
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');