<?php

use App\Livewire\Admin\Categories;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/categories', Categories::class)->name('admin.categories');
});

require __DIR__.'/settings.php';
