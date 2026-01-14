<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Admin routes
    Route::middleware('can:viewAny,App\Models\User')->group(function () {
        Route::get('/admin/users/pending', [AdminController::class, 'pendingUsers']);
        Route::get('/admin/users', [AdminController::class, 'allUsers']);
        Route::post('/admin/users/{id}/approve', [AdminController::class, 'approveUser']);
        Route::delete('/admin/users/{id}/reject', [AdminController::class, 'rejectUser']);
    });
});
