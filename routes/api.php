<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\SchoolController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// =============================================================================
// PUBLIC ROUTES (No authentication required)
// =============================================================================

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Schools, Levels, Modules (Public catalog data)
Route::get('/schools', [SchoolController::class, 'index']);
Route::get('/schools/{school}', [SchoolController::class, 'show']);
Route::get('/levels', [LevelController::class, 'index']);
Route::get('/levels/{level}', [LevelController::class, 'show']);
Route::get('/modules', [ModuleController::class, 'index']);
Route::get('/modules/{module}', [ModuleController::class, 'show']);

// Materials (Public browsing)
Route::get('/materials', [MaterialController::class, 'index']);
Route::get('/materials/{note}', [MaterialController::class, 'show']);

// =============================================================================
// PROTECTED ROUTES (Require Sanctum authentication)
// =============================================================================

Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/user', [AuthController::class, 'user']);

    // User's own notes (CRUD)
    Route::get('/my-notes', [NoteController::class, 'myNotes']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

    // Purchases
    Route::get('/my-purchases', [PurchaseController::class, 'index']);
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show']);
    Route::get('/purchases/{purchase}/download', [PurchaseController::class, 'download']);

    // Cart & Checkout
    Route::post('/cart', [CartController::class, 'index']);
    Route::get('/cart/validate/{note}', [CartController::class, 'validateItem']);
    Route::post('/checkout', [CartController::class, 'checkout']);
    Route::post('/checkout/verify', [CartController::class, 'verifyCheckout']);
});
