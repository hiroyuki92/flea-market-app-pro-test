<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;

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

Route::get('/', [ItemController::class, 'index']);
Route::get('/sell', [ItemController::class, 'create']);
Route::get('/item', [ItemController::class, 'show']);
Route::get('/purchase', [ItemController::class, 'purchase']);
Route::get('/purchase/address', [ItemController::class, 'update']);
Route::get('/mypage', [ProfileController::class, 'show']);
Route::get('/mypage/profile', [ProfileController::class, 'update']);