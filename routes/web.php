<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('add-to-cart', [ProductController::class, 'addToCart'])->name('add.to.cart');
Route::get('cart', [ProductController::class, 'cart'])->name('cart');
Route::patch('update-cart', [ProductController::class, 'update'])->name('update.cart');
Route::delete('remove-from-cart', [ProductController::class, 'remove'])->name('remove.from.cart');
//Route::get('checkout', [ProductController::class, 'checkout'])->name('checkout.index');
Route::get('/order-save', [ProductController::class, 'placeOrder'])->name('save.order');
Route::get('order', [ProductController::class, 'orderlist'])->name('order.index');
Route::get('order-detail/{id}', [ProductController::class, 'orderdetail'])->name('order.detail');
Route::post('save-notification', [ProductController::class, 'saveNotification'])->name('send-notification');
Route::get('latest-notification', [ProductController::class, 'latestNotification'])->name('latest-notification');