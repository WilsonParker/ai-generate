<?php

namespace App\Providers;

use App\Models\Generate\ImageToImageGenerate;
use App\Models\Generate\TextGenerate;
use App\Models\Generate\TextToImageGenerate;
use App\Models\Prompt\Prompt;
use App\Models\User\UserInformation;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\Stock\StockGenerate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Collection::macro('insert', function ($index, $value) {
            $this->splice($index, 0, $value);
            return $this;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::loadKeysFrom(__DIR__ . '/../../storage/oauth');
        Relation::morphMap([
            'prompt'                   => Prompt::class,
            'stock'                    => Stock::class,
            'stock-generates'          => StockGenerate::class,
            'text-generates'           => TextGenerate::class,
            'text-to-image-generates'  => TextToImageGenerate::class,
            'image-to-image-generates' => ImageToImageGenerate::class,
            'user-information'         => UserInformation::class,
            'App\Models\Stocks\Stock'  => \App\Models\Stock\AI\Stock::class,
        ]);
    }

}
