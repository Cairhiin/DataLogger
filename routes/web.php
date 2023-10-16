<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\LogAppController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LogModelController;
use App\Http\Controllers\LogRouteController;
use App\Http\Controllers\MessageController;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'auth.role'
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::prefix('event')->middleware('auth:sanctum')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('event.messages.index');
    Route::get('/logs', [LogController::class, 'index'])->name('event.logs.index');

    // Routes to get a list of unique route, model and app names
    Route::get('/logs/routes', [LogRouteController::class, 'index'])->name('event.logs.routes.index');
    Route::get('/logs/apps', [LogAppController::class, 'index'])->name('event.logs.apps.index');
    Route::get('/logs/models', [LogModelController::class, 'index'])->name('event.logs.models.index');

    Route::get('/logs/{id}', [LogController::class, 'show'])->name('event.logs.show');
    Route::delete('/logs/{id}', [LogController::class, 'destroy'])->name('event.logs.destroy');
});
