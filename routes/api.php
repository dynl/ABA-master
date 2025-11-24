<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AppointmentsController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\VaccineStockController;
// ===================================
// PUBLIC ROUTES
// ===================================
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [RegisterController::class, 'login']);
Route::get('/appointments/availability', [AppointmentsController::class, 'getAvailability']);
// Public routes (or protect with middleware if needed)
Route::post('/vaccines/stock', [VaccineStockController::class, 'store']);
Route::get('/vaccines/stock', [VaccineStockController::class, 'index']);
// --- NEW: PUBLIC IMAGE ROUTE (Fixes CORS) ---
// This allows the browser to fetch the image without blocking it
Route::get('/avatars/{filename}', [ProfileController::class, 'getAvatar']);

// ===================================
// PROTECTED ROUTES
// ===================================
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [ProfileController::class, 'user']); // Get Profile
    Route::post('/user/avatar', [ProfileController::class, 'updateAvatar']); // Upload Avatar

    Route::get('/appointments', [AppointmentsController::class, 'index']);
    Route::post('/appointments', [AppointmentsController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentsController::class, 'show']);
    Route::put('/appointments/{id}', [AppointmentsController::class, 'update']);
    Route::delete('/appointments/{id}', [AppointmentsController::class, 'destroy']);
});
