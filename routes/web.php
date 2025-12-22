<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookIntentController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;   
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaketWisataController;
use App\Http\Controllers\PokdarwisController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Models\Pokdarwis;
use Illuminate\Support\Facades\Storage;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);

// Index
Route::get('/pokdarwis', [PokdarwisController::class, 'index'])->name('pokdarwis');

// Booking per pokdarwis
Route::get('/tour/{pokdarwis:slug}/booking', [BookingController::class, 'forPokdarwis'])
     ->name('booking.pokdarwis');

// Booking per paket di pokdarwis
Route::get('/tour/{pokdarwis:slug}/paket/{paket:slug}/booking', [BookingController::class, 'forPackage'])
     ->name('booking.package');

     
     
// Detail pakai slug
Route::get('/tour/{pokdarwis:slug}', [PokdarwisController::class, 'show'])
    ->name('pokdarwis.show');

// (opsional tapi sangat membantu) redirect kalau ada yang ngetik ID:
Route::get('/tour/{id}', function ($id) {
    $pd = Pokdarwis::findOrFail($id);
    return redirect()->route('pokdarwis.show', ['pokdarwis' => $pd->slug]);
})->whereNumber('id');

// Paket Wisata
Route::get('/paket', [PaketWisataController::class, 'index'])->name('paket.index');
Route::get('/paket/{paket:slug}', [PaketWisataController::class, 'show'])->name('paket.show');

Route::post('/paket/{paket:slug}/book-intent', [BookIntentController::class, 'store'])
    ->name('paket.book.intent');

// Produk
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// Gallery (cukup satu)
Route::get('/gallery', [GalleryController::class, 'index'])
     ->name('gallery');

// Blog
Route::get('/blogarchive', [BlogController::class, 'index'])->name('posts.index');
Route::get('/blogarchive/{slug}', [BlogController::class, 'show'])->name('posts.show');

//Review
Route::prefix('pokdarwis/{pokdarwis}')->group(function () {
  Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store'); // kirim review
});


Route::get('/storage/{path}', function (string $path) {
    abort_unless(Storage::disk('public')->exists($path), 404);
    return response()->file(Storage::disk('public')->path($path));
})->where('path', '.*');


// routes/web.php
Route::middleware(['auth']) // ganti sesuai nama role-mu
    ->post('/pokdarwis/{pokdarwis:slug}/reviews', [ReviewController::class, 'store'])
    ->name('pokdarwis.reviews.store');

require __DIR__.'/auth.php';













