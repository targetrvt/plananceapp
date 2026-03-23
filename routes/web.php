<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Stripe\StripeCheckoutController;
use App\Http\Controllers\Stripe\StripeWebhookController;

// Home page (landing page)
Route::get('/', function () {
    return view('landing'); // This will use the landing.blade.php view
});

// Redirect /home to / 
Route::redirect('/home', '/');
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

// Stripe demo (app-level monthly pricing)
Route::get('/stripe/checkout/{plan}', [StripeCheckoutController::class, 'checkout'])
    ->name('stripe.checkout');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);