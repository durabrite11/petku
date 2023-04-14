<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/forgot', [App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
Route::post('/setPassword', [App\Http\Controllers\Api\AuthController::class, 'setPassword']);
   
Route::middleware('auth:sanctum')->group( function () {
    Route::get('/getInfo', [App\Http\Controllers\Api\AuthController::class, 'getInfo']);
    Route::post('/user/update', [App\Http\Controllers\Api\MemberController::class, 'update']);
    Route::post('/user/setLocation', [App\Http\Controllers\Api\MemberController::class, 'setLocation']);

    Route::get('/bank/get', [App\Http\Controllers\Api\BankController::class, 'get']);

    Route::post('/topup/create', [App\Http\Controllers\Api\TopupController::class, 'topup']);
    Route::get('/topup/get', [App\Http\Controllers\Api\TopupController::class, 'get']);

    Route::get('/serviceType/get', [App\Http\Controllers\Api\ServiceTypeController::class, 'get']);
    Route::get('/serviceType/item/{id}', [App\Http\Controllers\Api\ServiceTypeController::class, 'item']);

    Route::get('/pet/get', [App\Http\Controllers\Api\PetController::class, 'get']);

    Route::get('/service/get', [App\Http\Controllers\Api\ServiceController::class, 'get']);
    Route::post('/service/save', [App\Http\Controllers\Api\ServiceController::class, 'save']);
    Route::get('/service/detail', [App\Http\Controllers\Api\ServiceController::class, 'detail']);
    Route::post('/service/favorite/set', [App\Http\Controllers\Api\ServiceController::class, 'setFavorite']);
    Route::get('/service/favorite/get', [App\Http\Controllers\Api\ServiceController::class, 'getFavorite']);

    Route::get('/groomer/get', [App\Http\Controllers\Api\GroomerController::class, 'get']);
    Route::get('/groomer/detail', [App\Http\Controllers\Api\GroomerController::class, 'detail']);

    
    Route::post('/transaction/create', [App\Http\Controllers\Api\TransactionController::class, 'create']);
    Route::get('/transaction/get', [App\Http\Controllers\Api\TransactionController::class, 'get']);
    Route::post('/transaction/confirm', [App\Http\Controllers\Api\TransactionController::class, 'confirm']);


    Route::get('/balance/get', [App\Http\Controllers\Api\BalanceController::class, 'get']);

    Route::get('/summary/dashboard', [App\Http\Controllers\Api\SummaryController::class, 'dashboard']);
    Route::get('/summary/report', [App\Http\Controllers\Api\SummaryController::class, 'report']);


    Route::get('/chat/getMessage', [App\Http\Controllers\Api\ChatController::class, 'getMessage']);
    Route::post('/chat/sendMessage', [App\Http\Controllers\Api\ChatController::class, 'sendMessage']);
    Route::get('/chat/get', [App\Http\Controllers\Api\ChatController::class, 'get']);
    Route::post('/chat/create', [App\Http\Controllers\Api\ChatController::class, 'create']);


    Route::post('/notification/sendNotification', [App\Http\Controllers\Api\NotificationController::class, 'sendNotification']);
    Route::get('/notification/get', [App\Http\Controllers\Api\NotificationController::class, 'get']);

    
    Route::post('/withdraw/create', [App\Http\Controllers\Api\WithdrawController::class, 'create']);
    Route::get('/withdraw/get', [App\Http\Controllers\Api\WithdrawController::class, 'get']);

    
    Route::post('/report/create', [App\Http\Controllers\Api\ReportController::class, 'create']);
    
    Route::post('/rating/create', [App\Http\Controllers\Api\RatingController::class, 'create']);
    Route::get('/rating/get', [App\Http\Controllers\Api\RatingController::class, 'get']);

    Route::post('/memberPet/create', [App\Http\Controllers\Api\MemberPetController::class, 'create']);
    Route::get('/memberPet/get', [App\Http\Controllers\Api\MemberPetController::class, 'get']);
    Route::post('/memberPet/delete', [App\Http\Controllers\Api\MemberPetController::class, 'delete']);

    
    Route::post('/transaction/schedule/change', [App\Http\Controllers\Api\TransactionChangeSchedule::class, 'change']);
    Route::post('/transaction/schedule/change/confirm', [App\Http\Controllers\Api\TransactionChangeSchedule::class, 'confirm']);
    Route::get('/transaction/schedule/change/get', [App\Http\Controllers\Api\TransactionChangeSchedule::class, 'get']);
});
