<?php

use App\Http\Controllers\AiGenerateController;
use Illuminate\Support\Facades\Route;
use \Illuminate\Http\Request;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Profile\ProfileController;
use App\Http\Controllers\Admin\Upload\PackagesController;
use App\Http\Controllers\Admin\Upload\ProductsController;
use App\Http\Controllers\Admin\Upload\PaketWisataController;
use App\Http\Controllers\Admin\Upload\MediaKontenController;
use App\Http\Controllers\Admin\Upload\PostsController;
use App\Http\Controllers\PaketFasilitasController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

//Route Admin.index
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])
        ->name('admin.index');
});

//Upload Product
Route::middleware(['auth'])->group(function () {
Route::get('/upload/product', [ProductsController::class, 'create'])->name('admin.upload.product.uploadProduct');
Route::post('/upload/product', [ProductsController::class, 'store'])->name('pokdarwis.product.store');
Route::get('/upload/product/list', [ProductsController::class, 'index'])->name('pokdarwis.product.index');
Route::put('/upload/product/{product}', [ProductsController::class, 'update'])->name('pokdarwis.product.update');
Route::delete('/upload/product/{product}', [ProductsController::class, 'destroy'])->name('pokdarwis.product.destroy');

//Paket
Route::get('/upload/paket/list',   [PaketWisataController::class,'index'])->name('pokdarwis.paket.index');
Route::post('/upload/paket',       [PaketWisataController::class,'store'])->name('pokdarwis.paket.store');
Route::put('/upload/paket/{paket}',[PaketWisataController::class,'update'])->name('pokdarwis.paket.update');
Route::delete('/upload/paket/{paket}', [PaketWisataController::class,'destroy'])->name('pokdarwis.paket.destroy');

// /pokdarwis  → alias lama 'pokdarwis'
Route::get('/', function () {return redirect()->route('pokdarwis.product.index');})->name('pokdarwis');

// /pokdarwis/home → alias lama 'pokdarwis.index'
Route::get('/home', function () {return redirect()->route('pokdarwis.product.index');})->name('pokdarwis.index');

Route::post('/pokdarwis/upload/paket', function(Request $r){dd('HIT ROUTE', $r->all());})->name('pokdarwis.paket.store');});

Route::middleware(['auth'])->group(function () {
    Route::get('/pokdarwis/upload/paket',  [PaketWisataController::class, 'create'])->name('pokdarwis.paket.create');
    Route::post('/pokdarwis/upload/paket', [PaketWisataController::class, 'store'])->name('pokdarwis.paket.store');
});

//Upload Packages
Route::middleware(['auth'])->group(function () {
Route::get('/upload/paket',  [PackagesController::class, 'create'])->name('admin.upload.paket.uploadPaket');
Route::post('/upload/paket', [PackagesController::class, 'store'])->name('pokdarwis.paket.store');
});


//Media Konten
Route::middleware(['auth'])->group(function () {
    Route::get('/upload/konten/list',   [MediaKontenController::class, 'index'])->name('pokdarwis.konten.index');
    Route::post('/upload/konten',       [MediaKontenController::class, 'store'])->name('pokdarwis.konten.store');
    Route::put('/upload/konten/{id}',   [MediaKontenController::class, 'update'])->name('pokdarwis.konten.update');
    Route::delete('/upload/konten/{id}',[MediaKontenController::class, 'destroy'])->name('pokdarwis.konten.destroy');
});

Route::middleware('auth')->group(function () {
    // Menampilkan profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

    // Form edit profil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // Update profil
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // BLOG / POSTS
    Route::get('/upload/posts/list',   [PostsController::class, 'index'])->name('pokdarwis.posts.index');
    Route::post('/upload/posts',       [PostsController::class, 'store'])->name('pokdarwis.posts.store');
    Route::put('/upload/posts/{post}', [PostsController::class, 'update'])->name('pokdarwis.posts.update');
    Route::delete('/upload/posts/{post}', [PostsController::class, 'destroy'])->name('pokdarwis.posts.destroy');
});


//Ai Generate
Route::post('/ai/generate', [AiGenerateController::class, 'generate'])
     ->name('ai.generate');
     

     //profile
     Route::prefix('pokdarwis')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('pokdarwis.profile');
});

//Fasilitas
Route::get('/paket/{paket}/fasilitas/edit', [PaketFasilitasController::class, 'edit'])
    ->name('paket.fasilitas.edit');

Route::post('/paket/{paket}/fasilitas', [PaketFasilitasController::class, 'storeOrUpdate'])
    ->name('paket.fasilitas.save');