<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;

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
    Route::post('/message', [FileController::class, 'store'])->name('event.message.store');
    Route::post('/log', [LogController::class, 'store'])->name('event.log.store');
});
