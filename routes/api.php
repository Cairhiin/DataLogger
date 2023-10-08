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
    Route::post('/message', [MessageController::class, 'create'])->name('event.message.create');
    Route::post('/log', [LogController::class, 'create'])->name('event.log.create');
});
