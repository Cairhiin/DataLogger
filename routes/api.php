<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MessageController;

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

Route::prefix('event')->middleware('auth:sanctum')->group(function () {
    Route::post('/message', [MessageController::class, 'store'])->name('event.message.store');
    Route::post('/log', [LogController::class, 'store'])->name('event.log.store');
    Route::post('/test', [LogController::class, 'testRabbitMQ'])->name('event.test.store');
});
