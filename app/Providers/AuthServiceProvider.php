<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Generate\ImageToImageGenerate;
use App\Models\Generate\TextGenerate;
use App\Models\Generate\TextToImageGenerate;
use App\Models\Prompt\Prompt;
use App\Models\User\User;
use App\Policies\Generate\ImageToImageGeneratePolicy;
use App\Policies\Generate\TextGeneratePolicy;
use App\Policies\Generate\TextToImageGeneratePolicy;
use App\Policies\Prompt\PromptPolicy;
use App\Policies\User\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'AIGenerate\Models\Model' => 'App\Policies\ModelPolicy',
        User::class                 => UserPolicy::class,
        Prompt::class               => PromptPolicy::class,
        TextGenerate::class         => TextGeneratePolicy::class,
        TextToImageGenerate::class  => TextToImageGeneratePolicy::class,
        ImageToImageGenerate::class => ImageToImageGeneratePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //        $this->registerPolicies();
        // Passport::loadKeysFrom(__DIR__ . '/../secrets/oauth');

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        $this->defineGate();
    }

    /**
     * Register any authentication / authorization services.
     */
    public function defineGate(): void
    {
        Gate::define('view-prompt', [PromptPolicy::class, 'view']);
    }
}
