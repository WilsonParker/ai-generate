<?php

namespace App\Providers;

use App\Models\Enterprise\EnterpriseRequest;
use App\Models\Generate\TextGenerateExport;
use App\Models\Prompt\Prompt;
use App\Models\User\User;
use App\Observers\Enterprise\EnterpriseRequestObserver;
use App\Observers\Generate\TextGenerateExportObserver;
use App\Observers\Prompt\PromptObserver;
use App\Observers\Prompt\PromptViewObserver;
use App\Observers\Stock\StockExportObserver;
use App\Observers\Stock\StockLikeObserver;
use App\Observers\Stock\StockReviewObserver;
use App\Observers\Stock\StockViewObserver;
use App\Observers\User\UserObserver;
use App\Observers\User\UserViewObserver;
use Illuminate\Support\ServiceProvider;
use AIGenerate\Models\Prompt\PromptView;
use AIGenerate\Models\Stock\StockExport;
use AIGenerate\Models\Stock\StockLike;
use AIGenerate\Models\Stock\StockReview;
use AIGenerate\Models\Stock\StockView;
use AIGenerate\Models\User\UserView;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        UserView::observe(UserViewObserver::class);
        Prompt::observe(PromptObserver::class);
        \AIGenerate\Models\Prompt\Prompt::observe(PromptObserver::class);
        PromptView::observe(PromptViewObserver::class);
        StockView::observe(StockViewObserver::class);
        StockLike::observe(StockLikeObserver::class);
        StockReview::observe(StockReviewObserver::class);
        StockExport::observe(StockExportObserver::class);
        TextGenerateExport::observe(TextGenerateExportObserver::class);
        EnterpriseRequest::observe(EnterpriseRequestObserver::class);
    }

}
