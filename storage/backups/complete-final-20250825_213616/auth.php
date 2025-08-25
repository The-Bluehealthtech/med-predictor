<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', function() {
        return view('auth.register');
    })->name('register');

    Route::post('register', function() {
        return redirect()->route('login');
    });

    Route::get('login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('login', [LoginController::class, 'login']);

    Route::get('forgot-password', function() {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('forgot-password', function() {
        return redirect()->route('login');
    })->name('password.email');

    Route::get('reset-password/{token}', function() {
        return view('auth.reset-password');
    })->name('password.reset');

    Route::post('reset-password', function() {
        return redirect()->route('login');
    })->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', function() {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', function() {
        return redirect()->route('verification.notice');
    })->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', function() {
        return redirect()->route('verification.notice');
    })->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', function() {
        return view('auth.confirm-password');
    })->name('password.confirm');

    Route::post('confirm-password', function() {
        return redirect()->route('login');
    });

    Route::put('password', function() {
        return redirect()->route('login');
    })->name('password.update');

    Route::post('logout', [LoginController::class, 'logout'])
        ->name('logout');
});
