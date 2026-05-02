<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MetricsController;
use Illuminate\Support\Facades\Route;

// Prometheus metrics endpoint (no auth, localhost only)
Route::get('/metrics', [MetricsController::class, 'index']);

// Guest routes (login)
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Protected dashboard routes
Route::middleware('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [DashboardController::class, 'users'])->name('dashboard.users');
    Route::get('/portfolio', [DashboardController::class, 'portfolio'])->name('dashboard.portfolio');
    Route::get('/server', [DashboardController::class, 'server'])->name('dashboard.server');
    Route::get('/logs', [DashboardController::class, 'logs'])->name('dashboard.logs');
});
