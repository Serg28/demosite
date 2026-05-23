<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', fn () => view('auth.login'))->name('login');
    Route::get('register', fn () => view('auth.register'))->name('register');
    Route::get('forgot-password', fn () => view('auth.forgot-password'))->name('password.request');
    Route::get('reset-password/{token}', fn () => view('auth.reset-password'))->name('password.reset');
    Route::get('otp', fn () => view('auth.otp-login'))->name('auth.otp');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('confirm-password', fn () => view('auth.confirm-password'))->name('password.confirm');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
        ->middleware('App\Http\Middleware\ValidateProvider')
        ->name('auth.socialite.redirect');
    Route::get('auth/{provider}/callback', [SocialiteController::class, 'callback'])
        ->middleware('App\Http\Middleware\ValidateProvider')
        ->name('auth.socialite.callback');
});
