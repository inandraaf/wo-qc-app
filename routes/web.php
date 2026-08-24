<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\QualityControlController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role-specific dashboards
    Route::middleware('role:ppic,super_admin')->group(function () {
        Route::get('/dashboard/ppic', [DashboardController::class, 'ppic'])->name('dashboard.ppic');
    });

    Route::middleware('role:operator,super_admin')->group(function () {
        Route::get('/dashboard/operator', [DashboardController::class, 'operator'])->name('dashboard.operator');
    });

    Route::middleware('role:qc,super_admin')->group(function () {
        Route::get('/dashboard/qc', [DashboardController::class, 'qc'])->name('dashboard.qc');
    });

    Route::middleware('role:manager,super_admin')->group(function () {
        Route::get('/dashboard/manager', [DashboardController::class, 'manager'])->name('dashboard.manager');
    });

    // Super Admin only
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdmin'])->name('dashboard.super_admin');
        Route::resource('users', UserController::class);
    });

    // Work Orders
    Route::middleware('role:ppic,super_admin')->group(function () {
        Route::resource('work-orders', WorkOrderController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update']);
    });

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('work-orders', WorkOrderController::class)->only(['destroy']);
    });

    // Productions
    Route::middleware('role:operator,super_admin')->group(function () {
        Route::resource('productions', ProductionController::class)->only(['index', 'store']);
    });

    // Quality Controls
    Route::middleware('role:qc,super_admin')->group(function () {
        Route::resource('quality-controls', QualityControlController::class)->only(['index', 'store']);
    });
});

require __DIR__.'/auth.php';
