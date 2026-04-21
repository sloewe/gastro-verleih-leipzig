<?php

use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Users;
use App\Livewire\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/categories', Categories::class)->name('admin.categories');
    Route::get('admin/products', Products::class)->name('admin.products');
    Route::get('admin/users', Users::class)->name('admin.users');
});

require __DIR__.'/settings.php';
