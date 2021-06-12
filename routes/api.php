<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::prefix('auth')->group(function() {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('refresh-access-token', [AuthController::class, 'refreshAccessToken']);
    Route::get('validate-verification-token/{verification_token}', [AuthController::class, 'validateVerificationToken'])->name('verification.token.validate');
    Route::get('resend-verification-link', [AuthController::class, 'resendVerificationLink']);
    Route::post('send-password-reset-link', [AuthController::class, 'sendPasswordResetLink']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});