<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [GoogleController::class, 'redirectToGoogle'])->name('login');
    Route::get('/test', [GoogleController::class, 'generateTestToken'])->name('generate-test-token');
    Route::prefix('google')->name('google.')->group(function () {
        Route::get('/', [GoogleController::class, 'redirectToGoogle'])->name('redirect');
        Route::get('callback', [GoogleController::class, 'handleGoogleCallbackAndRedirect'])->name('callback');
    });
});
