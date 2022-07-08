<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//ホーム画面
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//新規作成画面遷移
Route::get('/create', [HomeController::class, 'create'])->name('create');
//新規メモ保存
Route::post('/store', [HomeController::class, 'store'])->name('store');
//編集画面遷移
Route::get('/edit/{id}', [HomeController::class, 'edit'])->name('edit');
//メモ更新
Route::post('/update/{id}', [HomeController::class, 'update'])->name('update');
//削除
Route::post('/delete/{id}', [HomeController::class, 'delete'])->name('delete');
