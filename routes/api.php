<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\RouteController;
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

    // Routes to get a list of unique route, model and app names
    Route::get('logs/routes', [RouteController::class, 'index'])->name('event.logs.routes.index');
    Route::get('logs/apps', [AppController::class, 'index'])->name('event.logs.apps.index');
    Route::get('logs/models', [ModelController::class, 'index'])->name('event.logs.models.index');
});
