<?php

use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Inquiries;
use App\Livewire\Admin\Pages;
use App\Livewire\Admin\Products;
use App\Livewire\Admin\Users;
use App\Livewire\Home;
use App\Livewire\Public\CategoryProducts;
use App\Livewire\Public\ContentPage;
use App\Livewire\Public\InquiryCheckout;
use App\Livewire\Public\InquiryList;
use App\Livewire\Public\InquiryThankYou;
use App\Livewire\Public\ProductDetails;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/kategorie/{slug}', CategoryProducts::class)->name('category.show');
Route::get('/produkt/{slug}', ProductDetails::class)->name('product.show');
Route::get('/anfrageliste', InquiryList::class)->name('inquiry.list');
Route::get('/anfrage-absenden', InquiryCheckout::class)->name('inquiry.checkout');
Route::get('/anfrage/danke', InquiryThankYou::class)->name('inquiry.thank-you');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/categories', Categories::class)->name('admin.categories');
    Route::get('admin/products', Products::class)->name('admin.products');
    Route::get('admin/inquiries', Inquiries::class)->name('admin.inquiries');
    Route::get('admin/users', Users::class)->name('admin.users');
    Route::get('admin/pages', Pages::class)->name('admin.pages');
});

require __DIR__.'/settings.php';

Route::get('/{slug}', ContentPage::class)
    ->where('slug', '^(?!admin$|dashboard$|login$|logout$|register$|forgot-password$|reset-password$|verify-email$|two-factor-challenge$|settings$|livewire$)[A-Za-z0-9-]+$')
    ->name('content.page');
