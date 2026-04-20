<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect('/login');
});

// Ketua RT
Route::middleware(['auth', 'role:ketua_rt'])
    ->prefix('ketua')
    ->name('ketua.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'ketua'])
            ->name('dashboard');
        
        Route::resource('users', UserController::class);
    });

// Bendahara
Route::middleware(['auth', 'role:bendahara'])
    ->prefix('bendahara')
    ->name('bendahara.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'bendahara'])
            ->name('dashboard');
    });

// Warga
Route::middleware(['auth', 'role:warga'])
    ->prefix('warga')
    ->name('warga.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'warga'])
            ->name('dashboard');
    });

require __DIR__.'/auth.php';