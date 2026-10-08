<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog');

Route::middleware('auth')->group(function () {
    // Breeze mengarahkan ke route 'dashboard' setelah login
    Route::get('/dashboard', fn () => redirect()->route('admin.products.index'))->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        // 'toggle' harus dideklarasikan SEBELUM resource
        Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
        Route::resource('products', ProductController::class)->except('show');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
