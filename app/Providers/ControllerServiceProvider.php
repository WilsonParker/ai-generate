<?php

namespace App\Providers;

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Personalize\PersonalizeController;
use App\Http\Controllers\Prompt\ChatPromptGenerateController;
use App\Http\Controllers\Prompt\CompletionPromptGenerateController;
use App\Http\Controllers\Prompt\ImagePromptGenerateController;
use App\Http\Controllers\Prompt\PromptFavoriteController;
use App\Http\Controllers\Stock\StockController;
use App\Http\Controllers\Stock\StockFavoriteController;
use App\Http\Controllers\Stock\StockGenerateController;
use App\Http\Controllers\Stock\StockLikeController;
use App\Http\Controllers\Stock\StockReviewController;
use App\Http\Controllers\Stripe\StripeController;
use App\Http\Controllers\Stripe\StripeHookController;
use App\Http\Controllers\User\UserController;
use App\Http\Repositories\Blog\BlogRepository;
use App\Http\Repositories\Enterprise\EnterpriseRepository;
use App\Http\Repositories\Generate\ImageToImageGenerateExportRepository;
use App\Http\Repositories\Generate\ImageToImageRepository;
use App\Http\Repositories\Generate\TextGenerateExportRepository;
use App\Http\Repositories\Generate\TextGenerateRepository;
use App\Http\Repositories\Generate\TextToImageGenerateExportRepository;
use App\Http\Repositories\Generate\TextToImageRepository;
use App\Http\Repositories\OpenAI\OpenAiKeyRepository;
use App\Http\Repositories\Point\PointHistoryRepository;
use App\Http\Repositories\Prompt\Contracts\PromptRepositoryContract;
use App\Http\Repositories\Prompt\PromptCategoryRepository;
use App\Http\Repositories\Prompt\PromptEngineRepository;
use App\Http\Repositories\Prompt\PromptFavoriteRepository;
use App\Http\Repositories\Prompt\PromptGenerateOutputOptionRepository;
use App\Http\Repositories\Prompt\PromptGenerateRepository;
use App\Http\Repositories\Prompt\PromptGenerateResultRepository;
use App\Http\Repositories\Prompt\PromptInfinityScrollRepository;
use App\Http\Repositories\Prompt\PromptOptionRepository;
use App\Http\Repositories\Prompt\PromptTypeRepository;
use App\Http\Repositories\Prompt\PromptViewRepository;
use App\Http\Repositories\Stock\StockExportRepository;
use App\Http\Repositories\Stock\StockFavoriteRepository;
use App\Http\Repositories\Stock\StockFilterRepository;
use App\Http\Repositories\Stock\StockGenerateRepository;
use App\Http\Repositories\Stock\StockLikeRepository;
use App\Http\Repositories\Stock\StockRecommendRepository;
use App\Http\Repositories\Stock\StockRepository;
use App\Http\Repositories\Stock\StockReviewRepository;
use App\Http\Repositories\Stock\StockSimilarSearchRepository;
use App\Http\Repositories\Stock\StockSimilarSearchWithCacheRepository;
use App\Http\Repositories\Stock\StockViewRepository;
use App\Http\Repositories\Stripe\StripeWebhookRepository;
use App\Http\Repositories\User\UserConstantRepository;
use App\Http\Repositories\User\UserRepository;
use App\Http\Repositories\User\UserViewRepository;
use App\Http\Response\Paginator;
use App\Http\Response\ResponseTemplate;
use App\Http\Response\SimplePaginator;
use App\Models\Blog\Blog;
use App\Models\Enterprise\Enterprise;
use App\Models\Generate\ImageToImageGenerate;
use App\Models\Generate\ImageToImageGenerateExport;
use App\Models\Generate\TextGenerateExport;
use App\Models\Generate\TextToImageGenerate;
use App\Models\Generate\TextToImageGenerateExport;
use App\Models\Prompt\Prompt;
use App\Models\User\User;
use App\Modules\Services\Generate\src\ImageToImageService;
use App\Services\Auth\AuthService;
use App\Services\Blog\BlogService;
use App\Services\Enterprise\Contracts\EnterpriseRepositoryContract;
use App\Services\Enterprise\Contracts\EnterpriseServiceContract;
use App\Services\Enterprise\EnterpriseService;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Mail\MailService;
use App\Services\Personalize\Contracts\PersonalizeServiceContract;
use App\Services\Personalize\PersonalizeService;
use App\Services\Personalize\Repositories\PersonalizeRepository;
use App\Services\Prompt\Contracts\PromptGenerateServiceContract;
use App\Services\Prompt\Contracts\PromptServiceContract;
use App\Services\Prompt\FreeGenerateComposite\Contracts\CanGenerateForFree;
use App\Services\Prompt\FreeGenerateComposite\FirstGenerateComposite;
use App\Services\Prompt\FreeGenerateComposite\FirstGenerateUsingConstantComposite;
use App\Services\Prompt\FreeGenerateComposite\GenerateComposite;
use App\Services\Prompt\PromptChatGenerateService;
use App\Services\Prompt\PromptCompletionGenerateService;
use App\Services\Prompt\PromptDecorators\Decorators;
use App\Services\Prompt\PromptDecorators\LastOrderDecorator;
use App\Services\Prompt\PromptDecorators\NullableAnswerDecorator;
use App\Services\Prompt\PromptFavoriteService;
use App\Services\Prompt\PromptGenerateService;
use App\Services\Prompt\PromptImageGenerateService;
use App\Services\Prompt\PromptInfinityService;
use App\Services\Prompt\Sorts\HottestSort;
use App\Services\Prompt\Sorts\NewestSort;
use App\Services\Prompt\Sorts\OldestSort;
use App\Services\Prompt\Sorts\RelevanceSort;
use App\Services\Prompt\Sorts\Sorts;
use App\Services\Prompt\Sorts\TopSort;
use App\Services\Prompt\ThumbnailComposite\DallEThumbnailComposite;
use App\Services\Prompt\ThumbnailComposite\DefaultThumbnailComposite;
use App\Services\Prompt\ThumbnailComposite\ThumbnailComposite;
use App\Services\SiteMap\SiteMapService;
use App\Services\Tag\TagService;
use App\Services\User\Contracts\UserServiceContract;
use App\Services\User\UserConstantService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;
use AIGenerate\Models\Generate\TextGenerate;
use AIGenerate\Models\OpenAI\OpenAiKey;
use AIGenerate\Models\OpenAI\OpenAiKeyStack;
use AIGenerate\Models\Payment\PointHistory;
use AIGenerate\Models\Prompt\PromptCategory;
use AIGenerate\Models\Prompt\PromptEngine;
use AIGenerate\Models\Prompt\PromptFavorite;
use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\Prompt\PromptGenerateResult;
use AIGenerate\Models\Prompt\PromptOption;
use AIGenerate\Models\Prompt\PromptType;
use AIGenerate\Models\Prompt\PromptView;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\Stock\StockExport;
use AIGenerate\Models\Stock\StockFavorite;
use AIGenerate\Models\Stock\StockFilter;
use AIGenerate\Models\Stock\StockGenerate;
use AIGenerate\Models\Stock\StockLike;
use AIGenerate\Models\Stock\StockRecommend;
use AIGenerate\Models\Stock\StockReview;
use AIGenerate\Models\Stock\StockView;
use AIGenerate\Models\Stripe\StripeWebhookLog;
use AIGenerate\Models\User\UserConstant;
use AIGenerate\Models\User\UserView;
use AIGenerate\Services\AI\OpenAI\Chat\ApiService;
use AIGenerate\Services\Exceptions\ExceptionService;
use AIGenerate\Services\Exceptions\Loggers\Contracts\ExceptionServiceContract;
use AIGenerate\Services\Generate\Contracts\ImageToImageServiceContract;
use AIGenerate\Services\Generate\Contracts\TextGenerateServiceContract;
use AIGenerate\Services\Generate\Contracts\TextToImageServiceContract;
use AIGenerate\Services\Generate\TextGenerateService;
use AIGenerate\Services\Generate\TextToImageService;
use AIGenerate\Services\Keyword\KeywordFilterService;
use AIGenerate\Services\Stock\Contracts\StockExportRepositoryContract;
use AIGenerate\Services\Stock\Contracts\StockFavoriteServiceContract;
use AIGenerate\Services\Stock\Contracts\StockGenerateServiceContract;
use AIGenerate\Services\Stock\Contracts\StockLikeServiceContract;
use AIGenerate\Services\Stock\Contracts\StockRecommendServiceContract;
use AIGenerate\Services\Stock\Contracts\StockRepositoryContract;
use AIGenerate\Services\Stock\Contracts\StockReviewServiceContract;
use AIGenerate\Services\Stock\Contracts\StockServiceContract;
use AIGenerate\Services\Stock\StockFavoriteService;
use AIGenerate\Services\Stock\StockGenerateService;
use AIGenerate\Services\Stock\StockLikeService;
use AIGenerate\Services\Stock\StockRecommendService;
use AIGenerate\Services\Stock\StockReviewService;
use AIGenerate\Services\Stock\StockService;
use AIGenerate\Services\Stripe\Contracts\StripeHookProcessorServiceContract;
use AIGenerate\Services\Stripe\Contracts\StripeHookServiceContract;
use AIGenerate\Services\Stripe\Contracts\StripeRepositoryServiceContract;
use AIGenerate\Services\Stripe\Repositories\StripeConnectRepository;
use AIGenerate\Services\Stripe\Repositories\StripeCustomerRepository;
use AIGenerate\Services\Stripe\StripeHookProcessorService;
use AIGenerate\Services\Stripe\StripeHookService;
use AIGenerate\Services\Stripe\StripeRepositoryService;
use AIGenerate\Services\Stripe\StripeService;
use AIGenerate\Services\Stripe\StripeServiceContract;

class ControllerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerFacades();
        $this->registerUser();
        $this->registerMail();
        $this->registerPrompt();
        $this->registerPoint();
        $this->registerStripe();
        $this->registerPersonalize();
        $this->registerStock();
        $this->registerGenerate();
        $this->registerBlog();
        $this->registerEnterprise();
        $this->registerException();
    }

    private function registerFacades()
    {
        $this->app->singleton('responseTemplate', function ($app) {
            return new ResponseTemplate();
        });
        $this->app->singleton('paginator', function ($app) {
            return new Paginator();
        });
        $this->app->singleton('simplePaginator', function ($app) {
            return new SimplePaginator();
        });
        $this->app->singleton('authService', function ($app) {
            return new AuthService($app->make(UserRepository::class));
        });
        $this->app->singleton('siteMapService', function ($app) {
            return new SiteMapService();
        });
    }

    private function registerUser()
    {
        $this->app->singleton(
            UserRepository::class,
            fn() => new UserRepository(User::class),
        );
        $this->app->singleton(
            UserViewRepository::class,
            fn() => new UserViewRepository(UserView::class),
        );
        $this->app->singleton(
            UserConstantRepository::class,
            fn($app) => new UserConstantRepository(UserConstant::class),
        );

        $this->app->singleton(
            UserService::class,
            fn($app) => new UserService(
                $app->make(UserRepository::class),
                $app->make(ImageServiceContract::class),
            ),
        );
        $this->app->singleton(
            UserConstantService::class,
            fn($app) => new UserConstantService(
                $app->make(UserConstantRepository::class),
                $app->make(PromptGenerateService::class),
            ),
        );

        $this->app->when(UserController::class)
                  ->needs(UserServiceContract::class)
                  ->give(UserService::class);
        $this->app->when(GoogleController::class)
                  ->needs(UserServiceContract::class)
                  ->give(UserService::class);
    }

    private function registerMail()
    {
        $this->app->singleton(
            MailService::class,
            fn($app) => new MailService(
                $app->make(UserConstantService::class),
            ),
        );
    }

    private function registerPrompt()
    {
        // DECORATORS
        $this->app->singleton(
            Decorators::class,
            fn($app) => new Decorators([
                $this->app->make(LastOrderDecorator::class),
                $this->app->make(NullableAnswerDecorator::class),
            ]),
        );

        // REPOSITORIES
        /*$this->app->singleton(
            PromptRepositoryContract::class,
            fn() => new PromptRepository(Prompt::class)
        );*/
        $this->app->singleton(
            PromptRepositoryContract::class,
            fn() => new PromptInfinityScrollRepository(Prompt::class),
        );
        $this->app->singleton(
            PromptOptionRepository::class,
            fn() => new PromptOptionRepository(PromptOption::class),
        );
        $this->app->singleton(
            PromptCategoryRepository::class,
            fn() => new PromptCategoryRepository(PromptCategory::class),
        );
        $this->app->singleton(
            PromptTypeRepository::class,
            fn() => new PromptTypeRepository(PromptType::class),
        );
        $this->app->singleton(
            PromptEngineRepository::class,
            fn() => new PromptEngineRepository(PromptEngine::class),
        );
        $this->app->singleton(
            PromptFavoriteRepository::class,
            fn() => new PromptFavoriteRepository(PromptFavorite::class),
        );
        $this->app->singleton(
            PromptGenerateRepository::class,
            fn() => new PromptGenerateRepository(PromptGenerate::class),
        );
        $this->app->singleton(
            PromptGenerateResultRepository::class,
            fn() => new PromptGenerateResultRepository(PromptGenerateResult::class),
        );
        $this->app->singleton(
            PromptGenerateOutputOptionRepository::class,
            fn() => new PromptGenerateOutputOptionRepository(''),
        );
        $this->app->singleton(
            PromptViewRepository::class,
            fn() => new PromptViewRepository(PromptView::class),
        );
        $this->app->singleton(
            OpenAiKeyRepository::class,
            fn() => new OpenAiKeyRepository(OpenAIKey::class, OpenAiKeyStack::class),
        );

        // COMPOSITE
        $this->app->singleton(
            FirstGenerateComposite::class,
            fn($app) => new FirstGenerateComposite(
                $this->app->make(PromptGenerateRepository::class),
            ),
        );
        $this->app->singleton(
            FirstGenerateUsingConstantComposite::class,
            fn($app) => new FirstGenerateUsingConstantComposite(
                $this->app->make(UserConstantService::class),
            ),
        );
        $this->app->singleton(
            CanGenerateForFree::class,
            fn($app) => new GenerateComposite([
                $this->app->make(FirstGenerateUsingConstantComposite::class),
            ]),
        );

        $this->app->singleton(
            DallEThumbnailComposite::class,
            fn($app) => new DallEThumbnailComposite(),
        );
        $this->app->singleton(
            DefaultThumbnailComposite::class,
            fn($app) => new DefaultThumbnailComposite(
                $this->app->make(ImageServiceContract::class),
            ),
        );
        $this->app->singleton(
            ThumbnailComposite::class,
            fn($app) => new ThumbnailComposite([
                $this->app->make(DallEThumbnailComposite::class),
                $this->app->make(DefaultThumbnailComposite::class),
            ]),
        );

        // SERVICES
        $this->app->singleton(
            PromptImageGenerateService::class,
            fn($app) => new PromptImageGenerateService(
                $app->make(PromptGenerateRepository::class),
                $app->make(PromptGenerateResultRepository::class),
                $app->make(OpenAiKeyRepository::class),
                $app->make(\AIGenerate\Services\AI\OpenAI\Images\ApiService::class),
            ),
        );
        $this->app->when(ImagePromptGenerateController::class)
                  ->needs(PromptGenerateServiceContract::class)
                  ->give(PromptImageGenerateService::class);

        $this->app->singleton(
            PromptChatGenerateService::class,
            fn($app) => new PromptChatGenerateService(
                $app->make(PromptGenerateRepository::class),
                $app->make(PromptGenerateResultRepository::class),
                $app->make(OpenAiKeyRepository::class),
                $app->make(ApiService::class),
                $app->make(Decorators::class),
            ),
        );
        $this->app->when(ChatPromptGenerateController::class)
                  ->needs(PromptGenerateServiceContract::class)
                  ->give(PromptChatGenerateService::class);

        $this->app->singleton(
            PromptCompletionGenerateService::class,
            fn($app) => new PromptCompletionGenerateService(
                $app->make(PromptGenerateRepository::class),
                $app->make(PromptGenerateResultRepository::class),
                $app->make(OpenAiKeyRepository::class),
                $app->make(\AIGenerate\Services\AI\OpenAI\Completion\ApiService::class),
                $app->make(Decorators::class),
            ),
        );
        $this->app->when(CompletionPromptGenerateController::class)
                  ->needs(PromptGenerateServiceContract::class)
                  ->give(PromptCompletionGenerateService::class);

        $this->app->singleton(
            PromptServiceContract::class,
            fn($app) => new PromptInfinityService(
                $app->make(PromptRepositoryContract::class),
                $app->make(PromptCategoryRepository::class),
                $app->make(PromptTypeRepository::class),
                $app->make(PromptEngineRepository::class),
                $app->make(PromptGenerateOutputOptionRepository::class),
                $app->make(TagService::class),
                $app->make(ImageServiceContract::class),
                new Sorts([
                    $app->make(RelevanceSort::class),
                    $app->make(HottestSort::class),
                    $app->make(TopSort::class),
                    $app->make(NewestSort::class),
                    $app->make(OldestSort::class),
                ]),
                $app->make(ThumbnailComposite::class),
            ),
        );

        $this->app->singleton(
            PromptFavoriteService::class,
            fn($app) => new PromptFavoriteService(
                $app->make(PromptFavoriteRepository::class),
            ),
        );

        $this->app->when(PromptFavoriteController::class)
                  ->needs(PromptFavoriteService::class)
                  ->give(PromptFavoriteService::class);

        $this->app->bind(\AIGenerate\Services\Mails\Contracts\PromptServiceContract::class, PromptServiceContract::class);
    }

    private function registerPoint(): void
    {
        $this->app->singleton(PointHistoryRepository::class, fn() => new PointHistoryRepository(PointHistory::class));
    }

    private function registerStripe()
    {
        $this->app->singleton(
            StripeWebhookRepository::class,
            fn() => new StripeWebhookRepository(StripeWebhookLog::class),
        );

        $this->app->singleton(
            StripeRepositoryServiceContract::class,
            fn($app) => new StripeRepositoryService(
                $app->make(StripeCustomerRepository::class),
                $app->make(StripeConnectRepository::class),
            ),
        );

        $this->app->singleton(
            StripeHookService::class,
            fn($app) => new StripeHookService(
                $app->make(StripeRepositoryServiceContract::class),
                $app->make(StripeWebhookRepository::class),
                $app->make(PointHistoryRepository::class),
            ),
        );

        $this->app->singleton(
            StripeHookProcessorService::class,
            fn($app) => new StripeHookProcessorService(
                $app->make(StripeRepositoryServiceContract::class),
            ),
        );

        $this->app->when(StripeController::class)
                  ->needs(StripeServiceContract::class)
                  ->give(StripeService::class);

        $this->app->when(StripeController::class)
                  ->needs(StripeRepositoryServiceContract::class)
                  ->give(StripeRepositoryService::class);

        $this->app->when(StripeHookController::class)
                  ->needs(StripeHookServiceContract::class)
                  ->give(StripeHookService::class);

        $this->app->when(StripeHookController::class)
                  ->needs(StripeHookProcessorServiceContract::class)
                  ->give(StripeHookProcessorService::class);
    }

    private function registerPersonalize()
    {
        $this->app->singleton(
            PersonalizeService::class,
            fn($app) => new PersonalizeService(
                $app->make(PersonalizeRepository::class),
                $app->make(MailService::class),
            ),
        );

        $this->app->when(PersonalizeController::class)
                  ->needs(PersonalizeServiceContract::class)
                  ->give(PersonalizeService::class);
    }

    public function registerStock()
    {
        $this->app->singleton(
            StockRepository::class,
            fn() => new StockRepository(Stock::class),
        );

        $this->app->singleton(
            StockSimilarSearchWithCacheRepository::class,
            fn() => new StockSimilarSearchWithCacheRepository(Stock::class),
        );

        $this->app->singleton(
            StockSimilarSearchRepository::class,
            fn() => new StockSimilarSearchRepository(Stock::class),
        );

        $this->app->bind(
            StockRepositoryContract::class,
            fn($app) => $app->make(StockSimilarSearchWithCacheRepository::class),
        );

        $this->app->singleton(
            StockFilterRepository::class,
            fn() => new StockFilterRepository(StockFilter::class),
        );

        $this->app->singleton(
            StockRecommendRepository::class,
            fn() => new StockRecommendRepository(StockRecommend::class),
        );

        $this->app->singleton(
            StockViewRepository::class,
            fn() => new StockViewRepository(StockView::class),
        );

        $this->app->singleton(
            StockLikeRepository::class,
            fn() => new StockLikeRepository(StockLike::class),
        );

        $this->app->singleton(
            StockFavoriteRepository::class,
            fn() => new StockFavoriteRepository(StockFavorite::class),
        );

        $this->app->singleton(
            StockGenerateRepository::class,
            fn() => new StockGenerateRepository(StockGenerate::class),
        );

        $this->app->singleton(
            StockReviewRepository::class,
            fn() => new StockReviewRepository(StockReview::class),
        );

        $this->app->singleton(
            StockExportRepository::class,
            fn() => new StockExportRepository(StockExport::class),
        );

        $this->app->bind(
            StockExportRepositoryContract::class,
            fn($app) => $app->make(StockExportRepository::class),
        );

        $this->app->bind(
            StockServiceContract::class,
            fn($app) => $app->make(StockService::class),
        );

        $this->app->when(StockController::class)
                  ->needs(StockServiceContract::class)
                  ->give(StockService::class);

        $this->app->when(StockController::class)
                  ->needs(StockRecommendServiceContract::class)
                  ->give(StockRecommendService::class);

        $this->app->when(StockLikeController::class)
                  ->needs(StockLikeServiceContract::class)
                  ->give(StockLikeService::class);

        $this->app->when(StockFavoriteController::class)
                  ->needs(StockFavoriteServiceContract::class)
                  ->give(StockFavoriteService::class);

        $this->app->when(StockGenerateController::class)
                  ->needs(StockGenerateServiceContract::class)
                  ->give(StockGenerateService::class);

        $this->app->when(StockReviewController::class)
                  ->needs(StockReviewServiceContract::class)
                  ->give(StockReviewService::class);
    }

    private function registerGenerate(): void
    {
        $this->app->singleton(TextGenerateRepository::class, fn($app) => new TextGenerateRepository(TextGenerate::class));
        $this->app->singleton(TextGenerateExportRepository::class, fn($app) => new TextGenerateExportRepository(TextGenerateExport::class));
        $this->app->singleton(TextToImageRepository::class, fn($app) => new TextToImageRepository(TextToImageGenerate::class));
        $this->app->singleton(TextToImageGenerateExportRepository::class, fn($app) => new TextToImageGenerateExportRepository(TextToImageGenerateExport::class));
        $this->app->singleton(ImageToImageRepository::class, fn($app) => new ImageToImageRepository(ImageToImageGenerate::class));
        $this->app->singleton(ImageToImageGenerateExportRepository::class, fn($app) => new ImageToImageGenerateExportRepository(ImageToImageGenerateExport::class));

        $this->app->singleton(TextGenerateService::class,
            fn($app) => new TextGenerateService(
                $app->make(TextGenerateRepository::class),
                $app->make(TextGenerateExportRepository::class),
                $app->make(KeywordFilterService::class),
            ),
        );
        $this->app->singleton(TextToImageService::class,
            fn($app) => new TextToImageService(
                $app->make(TextToImageRepository::class),
                $app->make(TextToImageGenerateExportRepository::class),
            ),
        );
        $this->app->singleton(ImageToImageService::class,
            fn($app) => new ImageToImageService(
                $app->make(ImageToImageRepository::class),
                $app->make(ImageToImageGenerateExportRepository::class),
            ),
        );

        $this->app->bind(TextGenerateServiceContract::class, TextGenerateService::class);
        $this->app->bind(TextToImageServiceContract::class, TextToImageService::class);
        $this->app->bind(ImageToImageServiceContract::class, ImageToImageService::class);
    }

    private function registerBlog(): void
    {
        $this->app->singleton(
            BlogRepository::class,
            fn() => new BlogRepository(Blog::class),
        );

        $this->app->singleton(
            BlogService::class,
            fn($app) => new BlogService($app->make(BlogRepository::class)),
        );
    }

    private function registerEnterprise(): void
    {
        $this->app->singleton(
            EnterpriseRepository::class,
            fn() => new EnterpriseRepository(Enterprise::class),
        );

        $this->app->bind(EnterpriseRepositoryContract::class, EnterpriseRepository::class);

        $this->app->singleton(
            EnterpriseService::class,
            fn($app) => new EnterpriseService($app->make(EnterpriseRepositoryContract::class)),
        );

        $this->app->bind(EnterpriseServiceContract::class, EnterpriseService::class);
    }

    private function registerException(): void
    {
        $this->app->bind(
            ExceptionServiceContract::class,
            ExceptionService::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

}
