<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolController as AdminSchoolController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\MaterialController as AdminMaterialController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

use App\Http\Controllers\Student\NoteController;
use App\Http\Controllers\Student\PurchaseController;
use App\Http\Controllers\Student\CartController;

use App\Http\Controllers\BrowseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
| Only admins, never students
*/

Route::middleware(['auth', 'blocked', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/schools', [AdminSchoolController::class, 'store'])
            ->name('schools.store');
        Route::put('/schools/{school}', [AdminSchoolController::class, 'update'])
            ->name('schools.update');
        Route::delete('/schools/{school}', [AdminSchoolController::class, 'destroy'])
            ->name('schools.destroy');

        Route::post('/levels', [LevelController::class, 'store'])
            ->name('levels.store');
        Route::put('/levels/{level}', [LevelController::class, 'update'])
            ->name('levels.update');
        Route::delete('/levels/{level}', [LevelController::class, 'destroy'])
            ->name('levels.destroy');

        Route::post('/modules', [ModuleController::class, 'store'])
            ->name('modules.store');
        Route::put('/modules/{module}', [ModuleController::class, 'update'])
            ->name('modules.update');
        Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])
            ->name('modules.destroy');

        Route::post('/materials/{note}/approve', [AdminMaterialController::class, 'approve'])
            ->name('materials.approve');
        Route::post('/materials/{note}/pending', [AdminMaterialController::class, 'pending'])
            ->name('materials.pending');
        Route::delete('/materials/{note}', [AdminMaterialController::class, 'destroy'])
            ->name('materials.destroy');

        Route::post('/users/{user}/role', [AdminUserController::class, 'updateRole'])
            ->name('users.role');
        Route::post('/users/{user}/block', [AdminUserController::class, 'block'])
            ->name('users.block');
        Route::post('/users/{user}/unblock', [AdminUserController::class, 'unblock'])
            ->name('users.unblock');
    });

/*
|--------------------------------------------------------------------------
| STUDENT ROUTES
|--------------------------------------------------------------------------
| Logged-in, not blocked
*/

Route::middleware(['auth', 'blocked'])->group(function () {

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{note}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/remove/{note}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    Route::post('/purchase', [PurchaseController::class, 'store'])
        ->name('purchase.store');

    Route::get('/my-purchases', [PurchaseController::class, 'index'])
        ->name('purchases.index');

    Route::get('/upload-note', [NoteController::class, 'create'])->name('notes.create');

    Route::get('/my-notes', [NoteController::class, 'myNotes'])
        ->name('notes.mine');
    Route::post('/notes', [NoteController::class, 'store'])
        ->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])
        ->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])
        ->name('notes.destroy');

    Route::get('/my-purchases', [PurchaseController::class, 'index'])
        ->name('purchases.index');
});

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| No authentication required
*/

Route::get('/materials', [BrowseController::class, 'index'])
    ->name('materials.index');

Route::get('/materials/{note}', [BrowseController::class, 'show'])
    ->name('materials.show');
