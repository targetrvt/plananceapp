<?php

use Illuminate\Support\Facades\Route;

// Home page (landing page)
Route::get('/', function () {
    return view('landing'); // This will use the landing.blade.php view
});

// Redirect /home to / 
Route::redirect('/home', '/');
