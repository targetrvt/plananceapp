<?php

use App\Http\Controllers\DeactivateAccountController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileNotificationSettingsController;
use App\Http\Controllers\Stripe\StripeCheckoutController;
use App\Http\Controllers\Stripe\StripeWebhookController;
use App\Http\Controllers\TransactionReceiptController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth')->post('/stripe/subscription/resume', [StripeCheckoutController::class, 'resumeSubscription'])
    ->name('stripe.subscription.resume');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::middleware('auth')->post('{filament_panel}/my-profile/email-noticications', [ProfileNotificationSettingsController::class, 'update'])
    ->whereIn('filament_panel', ['app', 'premium'])
    ->name('profile.email-noticications.update');

Route::middleware('auth')->post('{filament_panel}/my-profile/deactivate-account', [DeactivateAccountController::class, 'store'])
    ->whereIn('filament_panel', ['app', 'premium'])
    ->name('profile.deactivate');

Route::middleware('auth')->get('/transactions/{transaction}/receipt', [TransactionReceiptController::class, 'show'])
    ->name('transactions.receipt.show');
