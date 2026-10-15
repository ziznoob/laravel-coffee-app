<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ProductController::class, 'home'])->name('homepage');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::delete('/', 'clear')->name('clear');
    Route::post('/items', 'store')->name('items.store');
    Route::patch('/items/{variant}', 'update')->name('items.update');
    Route::delete('/items/{variant}', 'destroy')->name('items.destroy');
});

Route::post('/cart/checkout', [CheckoutController::class, 'store'])->name('cart.checkout');
Route::get('/cart/success', [CheckoutController::class, 'success'])->name('cart.success');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/products', [ProductController::class, 'adminIndex'])->name('products');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
