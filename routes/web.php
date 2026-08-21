<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\QualityControlController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Work Orders
    Route::resource('work-orders', WorkOrderController::class);

    // Productions
    Route::resource('productions', ProductionController::class)->only(['index', 'store']);

    // Quality Controls
    Route::resource('quality-controls', QualityControlController::class)->only(['index', 'store']);
});

require __DIR__.'/auth.php';
