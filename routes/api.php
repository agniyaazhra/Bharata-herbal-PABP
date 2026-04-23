<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ───────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/products',        [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories',      [ProductController::class, 'categories']);

// ─── Authenticated Routes (Sanctum Token) ────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Cart
    Route::get('/cart',                        [CartController::class, 'index']);
    Route::post('/cart',                       [CartController::class, 'add']);
    Route::patch('/cart/{cartItem}',           [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}',          [CartController::class, 'remove']);
    Route::post('/cart/toggle-select-all',     [CartController::class, 'toggleSelectAll']);
    Route::patch('/cart/{cartItem}/toggle-select', [CartController::class, 'toggleSelect']);
    Route::delete('/cart',                     [CartController::class, 'clearAll']);

    // Checkout
    Route::get('/checkout',  [CheckoutController::class, 'index']);
    Route::post('/checkout', [CheckoutController::class, 'store']);

    // Orders
    Route::get('/orders',                     [OrderController::class, 'index']);
    Route::get('/orders/{order}',             [OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel',     [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/pay',        [OrderController::class, 'payNow']);
    Route::post('/orders/{order}/buy-again',  [OrderController::class, 'buyAgain']);

    // Reviews
    Route::post('/orders/{order}/reviews',    [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}',        [ReviewController::class, 'destroy']);

    // Profile
    Route::get('/profile',            [ProfileController::class, 'show']);
    Route::put('/profile',            [ProfileController::class, 'update']);
    Route::put('/profile/password',   [ProfileController::class, 'updatePassword']);

    // Addresses
    Route::get('/addresses',                   [AddressController::class, 'index']);
    Route::post('/addresses',                  [AddressController::class, 'store']);
    Route::delete('/addresses/{address}',      [AddressController::class, 'destroy']);
    Route::patch('/addresses/{address}/default', [AddressController::class, 'setDefault']);
});
