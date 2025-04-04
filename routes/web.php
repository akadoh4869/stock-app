<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupController;
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
    Route::post('/stock/switch/{type}/{groupId?}', [StockController::class, 'switchSpace'])->name('stock.switch');

    // ✅ カテゴリ関連
    Route::get('/category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/items', [CategoryController::class, 'showItems'])->name('category.items');
    Route::get('/history', [CategoryController::class, 'history'])->name('category.history');
    Route::post('/category/{id}/restore', [CategoryController::class, 'restore'])->name('category.restore');

    // ✅ アイテム保存（StockControllerにて処理）
    Route::post('/item/store', [StockController::class, 'store'])->name('item.store');
    Route::put('/items/{item}', [StockController::class, 'update'])->name('item.update');

    Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::delete('/items/{item}', [StockController::class, 'destroy'])->name('item.destroy');

    Route::get('/items/history', [StockController::class, 'history'])->name('item.history');



    // 招待ページ表示（検索）
    Route::get('/group/invite', [GroupController::class, 'invite'])->name('group.invite');
    // 招待送信処理
    Route::post('/group/invite/send', [GroupController::class, 'sendInvite'])->name('group.invite.send');
    Route::post('/invitation/respond', [GroupController::class, 'respond'])->name('invitation.respond');
    // 退出
    // Route::post('/group/leave', [GroupController::class, 'leave'])->name('group.leave');
    Route::post('/group/{groupId}/leave', [GroupController::class, 'leave'])->name('group.leave');

    // フォームを表示するルート（GET）
    Route::get('/group/create', [GroupController::class, 'create'])->name('group.create.form');

    // フォーム送信を処理するルート（POST）
    Route::post('/group/create', [GroupController::class, 'store'])->name('group.create');


    Route::delete('/inventory/{inventory}', [GroupController::class, 'destroy'])->name('inventory.destroy');

    Route::delete('/category/{id}/force-delete', [CategoryController::class, 'forceDelete'])->name('category.forceDelete');

    
    Route::get('/history', [CategoryController::class, 'history'])->name('category.history');
    Route::get('/history/{categoryId}', [CategoryController::class, 'historyByCategory'])->name('category.history.category');


    Route::post('/item/{id}/restore', [StockController::class, 'restore'])->name('item.restore');
    Route::delete('/item/{id}/force-delete', [StockController::class, 'forceDelete'])->name('item.forceDelete');

    // web.php
    // Route::post('/items/{id}/update-image', [StockController::class, 'updateImage'])->name('item.update.image');

    // Route::post('/items/{item}/image', [StockController::class, 'uploadImage'])->name('item.image.upload');
    Route::delete('/images/{image}', [StockController::class, 'deleteImage'])->name('item.image.delete');

    // Route::post('/items/{item}/image', [StockController::class, 'uploadImage'])->name('item.update.image');

    // Route::post('/items/{item}/image', [StockController::class, 'updateImage'])->name('item.update.image');

    Route::post('/items/{item}/image', [StockController::class, 'uploadImage'])->name('item.image.upload');
    
    // Route::post('/items/{item}/image-upload', [StockController::class, 'uploadImage'])->name('item.image.upload');

    Route::post('/items/{item}/image-upload', [StockController::class, 'uploadImage'])->name('item.image.upload');

});
