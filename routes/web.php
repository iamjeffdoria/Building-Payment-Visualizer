<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\StallController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
});

// Guest-only: show and submit the login form
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Any logged-in user
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/admin/buildings', [BuildingController::class, 'index'])->name('admin.buildings');
    Route::get('/admin/buildings/{building}', [BuildingController::class, 'show'])->name('admin.buildings.show');
    Route::post('/admin/buildings/{building}/stalls', [StallController::class, 'store'])->name('admin.stalls.store');
    Route::put('/admin/stalls/{stall}', [StallController::class, 'update'])->name('admin.stalls.update');
    Route::delete('/admin/stalls/{stall}', [StallController::class, 'destroy'])->name('admin.stalls.destroy');
});

// Superadmin only
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});