<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/top', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/switch-space/{type}/{groupId?}', [StockController::class, 'switchSpace'])->name('stock.switch.space');
    // Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::post('/stock/switch/{type}/{groupId?}', [StockController::class, 'switchSpace'])->name('stock.switch');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/items', [CategoryController::class, 'showItems'])->name('category.items');

});
