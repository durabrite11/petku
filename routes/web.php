<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\UnittestController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\WithdrawController;

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
    return view('login');
});

Route::post('/login', [AuthController::class, "Login"]);
Route::get('/logout', [AuthController::class, "Logout"]);
Route::get('/unittest/maps', [UnittestController::class, "maps"]);

Route::group(['prefix'=> 'admin', 'middleware'=>'authLogin'], function () {
    Route::resource('banner', BannerController::class);
    Route::get('banner/{banner}/activate', [BannerController::class, "activate"]);
    Route::resource('bank', BankController::class);
    Route::get('bank/{bank}/activate', [BankController::class, "activate"]);
    Route::resource('users', UserController::class);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('topup', [TopupController::class, "index"])->name("topup");
    Route::get('topup/confirm/{status}/{topup}', [TopupController::class, "confirm"]);
    Route::get('transaction', [TransactionController::class, "index"])->name("transaction");

    
    Route::resource('serviceType', ServiceTypeController::class);
    Route::resource('pet', PetController::class);
    
    Route::get('withdraw', [WithdrawController::class, "index"])->name("withdraw");
    Route::get('withdraw/confirm/{status}/{withdraw}', [WithdrawController::class, "confirm"]);

});