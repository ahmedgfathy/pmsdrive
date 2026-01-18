<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\FileRestrictionController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public share link download (no authentication required)
Route::get('/share/{token}', [ShareController::class, 'downloadViaPublicLink']);

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
    
    // Share routes
    Route::post('/files/{id}/share/user', [ShareController::class, 'shareWithUser']);
    Route::post('/files/{id}/share/link', [ShareController::class, 'generatePublicLink']);
    Route::post('/files/{id}/share/email', [ShareController::class, 'sendLinkByEmail']);
    Route::get('/files/shared', [ShareController::class, 'getSharedFiles']);
    Route::delete('/files/{fileId}/share/{userId}', [ShareController::class, 'removeShare']);

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/users/pending', [AdminController::class, 'pendingUsers']);
        Route::get('/users', [AdminController::class, 'allUsers']);
        Route::post('/users/{id}/approve', [AdminController::class, 'approveUser']);
        Route::delete('/users/{id}/reject', [AdminController::class, 'rejectUser']);
        Route::put('/users/{id}/quota', [AdminController::class, 'updateQuota']);

        Route::get('/file-restrictions', [FileRestrictionController::class, 'index']);
        Route::post('/file-restrictions', [FileRestrictionController::class, 'store']);
        Route::put('/file-restrictions/{rule}', [FileRestrictionController::class, 'update']);
        Route::delete('/file-restrictions/{rule}', [FileRestrictionController::class, 'destroy']);
    });

});
