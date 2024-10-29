<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::middleware('checkToken')->get('/user-data', [ProfileController::class, 'data_user_login']);

Route::middleware(['checkToken'])->prefix('games')->group(function () {
    Route::post('all', [GamesController::class, 'show_all_Games']);
    Route::post('addCart', [GamesController::class, 'addCart']);
    Route::post('showcart', [GamesController::class, 'showcart']);
    Route::post('islike', [GamesController::class, 'isLike']);
    Route::post('deletecart', [GamesController::class, 'deleteCart']);
    Route::post('deletelike', [GamesController::class, 'deleteLike']);
    Route::post('/show/{surname}', [GamesController::class, 'showBySlug']);
    Route::post('/comment/addComment', [GamesController::class, 'addComment']);
    Route::post('/comment/show', [GamesController::class, 'showcomment']);
});

Route::middleware(['checkToken'])->prefix('profile')->group(function () {
    Route::post('gameUser', [ProfileController::class, 'getUserGames']);

    Route::prefix('edit')->group(function () {
        Route::post('name', [ProfileController::class, 'EditUsername']);
        Route::post('avatar', [ProfileController::class, 'EditAvatar']);
    });
});

Route::middleware(['checkToken'])->prefix('transaksi')->group(function () {
    Route::post('/', [GamesController::class, 'addtransaksi']);
});
