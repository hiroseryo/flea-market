<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\StripeWebhookController;

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::get('/email/verify', [RegisterController::class, 'showVerifyEmail'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verifyEmail'])->middleware('auth', 'signed')->name('verification.verify');
Route::post('/email/resend', [RegisterController::class, 'resendVerifyEmail'])->middleware('auth', 'throttle:6,1')->name('verification.resend');

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('item/{item_id}/like', [LikeController::class, 'like'])->name('items.like');

    Route::post('item/{item_id}/comment', [CommentController::class, 'store'])->name('comments.store');

    Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('items/store', [ItemController::class, 'store'])->name('items.store');

    Route::get('item/{item_id}/checkout', [ItemController::class, 'checkoutForm'])->name('items.checkoutForm');
    Route::post('/item/{item_id}/purchase', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
    Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    Route::get('/purchase/address/{item_id}', [ProfileController::class, 'addressForm'])->name('items.address');
    Route::post('/purchase/address/{item_id}', [ProfileController::class, 'addressUpdate'])->name('items.addressUpdate');

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
});
