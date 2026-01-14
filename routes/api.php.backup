<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\StorageController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Storage routes
    Route::get('/storage/stats', [StorageController::class, 'stats']);
    Route::get('/storage/overview', [StorageController::class, 'overview']);
    
    // Folder routes
    Route::get('/folders', [FolderController::class, 'index']);
    Route::post('/folders', [FolderController::class, 'store']);
    Route::get('/folders/{id}', [FolderController::class, 'show']);
    Route::put('/folders/{id}', [FolderController::class, 'update']);
    Route::delete('/folders/{id}', [FolderController::class, 'destroy']);
    Route::get('/folders/{id}/breadcrumb', [FolderController::class, 'breadcrumb']);
    
    // File routes
    Route::get('/files', [FileController::class, 'index']);
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::get('/files/search', [FileController::class, 'search']);
    Route::get('/files/{id}', [FileController::class, 'show']);
    Route::get('/files/{id}/download', [FileController::class, 'download']);
    Route::put('/files/{id}', [FileController::class, 'update']);
    Route::delete('/files/{id}', [FileController::class, 'destroy']);
    Route::post('/files/delete-multiple', [FileController::class, 'destroyMultiple']);
    
    // Admin routes
    Route::middleware('can:viewAny,App\Models\User')->group(function () {
        Route::get('/admin/users/pending', [AdminController::class, 'pendingUsers']);
        Route::get('/admin/users', [AdminController::class, 'allUsers']);
        Route::post('/admin/users/{id}/approve', [AdminController::class, 'approveUser']);
        Route::delete('/admin/users/{id}/reject', [AdminController::class, 'rejectUser']);
    });
});
