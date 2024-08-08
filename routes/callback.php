<?php


use App\Http\Controllers\Callback\ImageToImageGenerateController;
use App\Http\Controllers\Callback\StockController;
use App\Http\Controllers\Callback\TextGenerateController;
use App\Http\Controllers\Callback\TextToImageGenerateController;

Route::prefix('/stock')->name('stock.')->group(function () {
    Route::post('/generated', [StockController::class, 'generated'])
         ->name('generated')
         ->middleware('throttle:stock');

    Route::post('/generated/thumbnail', [StockController::class, 'generatedThumbnail'])
         ->name('generated.thumbnail')
         ->middleware('throttle:stock');
});

Route::prefix('/text')->name('text.')->group(function () {
    Route::post('/generated', [TextGenerateController::class, 'generated'])
         ->name('generated')
         ->middleware('throttle:stock');
});

Route::prefix('/txt2img')->name('txt2img.')->group(function () {
    Route::post('/generated', [TextToImageGenerateController::class, 'generated'])
         ->name('generated')
         ->middleware('throttle:stock');
});

Route::prefix('/img2img')->name('img2img.')->group(function () {
    Route::post('/generated', [ImageToImageGenerateController::class, 'generated'])
         ->name('generated')
         ->middleware('throttle:stock');
});
