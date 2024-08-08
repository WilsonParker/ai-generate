<?php

use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Enterprise\EnterpriseController;
use App\Http\Controllers\Generate\ImageToImageGenerateController;
use App\Http\Controllers\Generate\TextGenerateController;
use App\Http\Controllers\Generate\TextToImageGenerateController;
use App\Http\Controllers\Personalize\PersonalizeController;
use App\Http\Controllers\Prompt\ChatPromptGenerateController;
use App\Http\Controllers\Prompt\CompletionPromptGenerateController;
use App\Http\Controllers\Prompt\ImagePromptGenerateController;
use App\Http\Controllers\Prompt\PromptController;
use App\Http\Controllers\Prompt\PromptFavoriteController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Stock\StockFavoriteController;
use App\Http\Controllers\Stock\StockGenerateController;
use App\Http\Controllers\Stock\StockLikeController;
use App\Http\Controllers\Stock\StockReviewController;
use App\Http\Controllers\Stripe\StripeController;
use App\Http\Controllers\Stripe\StripeHookController;
use App\Http\Controllers\User\PointController;
use App\Http\Controllers\User\UserController;
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

Route::prefix('/prompt')->name('prompt.')->group(function () {
    Route::prefix('/{prompt}')->group(function () {
        Route::put('/template', [PromptController::class, 'updateTemplate'])->name('update-template');
        Route::get('/template', [PromptController::class, 'showTemplate'])->name('show-template');
        Route::post('/template', [PromptController::class, 'previewTemplate'])->name('preview-template');
        Route::get('/owner', [PromptController::class, 'showForOwner'])->name('show-for-owner');

        Route::prefix('/generate')->name('generate.')->group(function () {
            Route::post('/image', [ImagePromptGenerateController::class, 'store'])->name('image');
            Route::post('/chat', [ChatPromptGenerateController::class, 'store'])->name('chat');
            Route::post('/completion', [CompletionPromptGenerateController::class, 'store'])->name('completion');

            Route::prefix('/expect')->name('expect.')->group(function () {
                Route::post('/image', [ImagePromptGenerateController::class, 'expectPoint'])->name('image');
                Route::post('/chat', [ChatPromptGenerateController::class, 'expectPoint'])->name('chat');
                Route::post('/completion', [CompletionPromptGenerateController::class, 'expectPoint'])
                     ->name('completion');
            });
        });
    });

    Route::prefix('/favorite')->name('favorite.')->group(function () {
        Route::get('/', [PromptFavoriteController::class, 'index'])->name('index');
        Route::post('/{prompt}', [PromptFavoriteController::class, 'store'])->name('store');
        Route::delete('/{prompt}', [PromptFavoriteController::class, 'delete'])->name('delete');
    });

    Route::get('/input-options', [PromptController::class, 'getInputOptions'])->name('get-output-options');
    Route::get('/searchable-values', [PromptController::class, 'searchableValues'])->name('searchable-values');
    Route::get('/main', [PromptController::class, 'main'])->name('main');
});
Route::resource('prompt', PromptController::class)->only(['index', 'show', 'create', 'store', 'destroy']);

Route::prefix('/user')->name('user.')->group(function () {
    Route::resource('point', PointController::class)->only(['index']);

    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/', [UserController::class, 'currentUser'])->name('current');
    Route::put('/', [UserController::class, 'currentUpdate'])->name('current-update');
    Route::post('/avatar', [UserController::class, 'currentUpdateAvatar'])->name('current-update-avatar');

    Route::prefix('/prompt')->name('prompt.')->group(function () {
        Route::prefix('/list')->name('list.')->group(function () {
            Route::get('/generate', [\App\Http\Controllers\User\PromptController::class, 'generateList'])
                 ->name('generate');
            Route::get('/create', [\App\Http\Controllers\User\PromptController::class, 'createList'])->name('create');
            Route::get('/selling', [\App\Http\Controllers\User\PromptController::class, 'sellingList'])
                 ->name('selling');
        });
    });
});


Route::prefix('/stock')->name('stock.')->group(function () {
    Route::get('/searchable-values', [StockController::class, 'searchableValues'])->name('searchable-values');
    Route::get('/{stock}/similar', [StockController::class, 'similar'])->name('similar');
    Route::post('/{stock}/generate', [StockController::class, 'generate'])->name('generate');

    Route::prefix('/like')->name('like.')->group(function () {
        Route::post('/{stock}', [StockLikeController::class, 'store'])->name('store');
        Route::delete('/{stock}', [StockLikeController::class, 'delete'])->name('delete');
    });

    Route::prefix('/favorite')->name('favorite.')->group(function () {
        Route::get('/', [StockFavoriteController::class, 'index'])->name('index');
        Route::post('/{stock}', [StockFavoriteController::class, 'store'])->name('store');
        Route::delete('/{stock}', [StockFavoriteController::class, 'delete'])->name('delete');
    });

    Route::prefix('/generate')->name('generate.')->group(function () {
        Route::get('/', [StockGenerateController::class, 'index'])->name('index');
        Route::get('/{stockGenerate}/export', [StockGenerateController::class, 'exportImageUrl'])->name('export');
    });

    Route::prefix('/review')->name('review.')->group(function () {
        Route::get('/selectable-reviews', [StockReviewController::class, 'selectableReviews'])
             ->name('selectable-reviews');
        Route::post('/{stock}', [StockReviewController::class, 'store'])->name('store');
        Route::delete('/{stock}', [StockReviewController::class, 'delete'])->name('delete');
    });
});

Route::resource('stock', StockController::class)->only(['index', 'show']);

Route::prefix('/generate')->name('generate.')->group(function () {
    Route::prefix('/text')->name('text.')->group(function () {
        Route::get('/forms', [TextGenerateController::class, 'forms'])->name('forms');
        Route::get('/', [TextGenerateController::class, 'index'])->name('index');
        Route::post('/', [TextGenerateController::class, 'generate'])->name('text');
        Route::delete('/{generate}', [TextGenerateController::class, 'destroy'])->name('destroy');
        Route::get('{textGenerate}/export', [TextGenerateController::class, 'exportImageUrl'])->name('export');
    });

    Route::prefix('/txt2img')->name('txt2img.')->group(function () {
        Route::get('/forms', [TextToImageGenerateController::class, 'forms'])->name('forms');
        Route::get('/', [TextToImageGenerateController::class, 'index'])->name('index');
        Route::post('/', [TextToImageGenerateController::class, 'generate'])->name('text');
        Route::delete('/{generate}', [TextToImageGenerateController::class, 'destroy'])->name('destroy');
        Route::get('{generate}/export', [TextToImageGenerateController::class, 'exportImageUrl'])->name('export');
    });

    Route::prefix('/img2img')->name('img2img.')->group(function () {
        Route::get('/forms', [ImageToImageGenerateController::class, 'forms'])->name('forms');
        Route::get('/', [ImageToImageGenerateController::class, 'index'])->name('index');
        Route::post('/', [ImageToImageGenerateController::class, 'generate'])->name('text');
        Route::delete('/{generate}', [ImageToImageGenerateController::class, 'destroy'])->name('destroy');
        Route::get('{generate}/export', [ImageToImageGenerateController::class, 'exportImageUrl'])->name('export');
    });
});