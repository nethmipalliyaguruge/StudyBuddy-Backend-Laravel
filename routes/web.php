<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Student\NoteController;
use App\Http\Controllers\Student\PurchaseController;
use App\Http\Controllers\Student\CartController;
use App\Http\Controllers\Student\StripeController;

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

    // Stripe payment routes
    Route::post('/checkout/stripe', [StripeController::class, 'checkout'])->name('stripe.checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');

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
    Route::post('/notes/{note}/restore', [NoteController::class, 'restore'])
        ->name('notes.restore');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])
        ->name('notes.destroy');

    Route::get('/my-purchases', [PurchaseController::class, 'index'])
        ->name('purchases.index');

    Route::get('/purchases/{purchase}/download', [PurchaseController::class, 'download'])
        ->name('purchases.download');
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
