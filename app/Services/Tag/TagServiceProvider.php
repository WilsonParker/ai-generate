<?php

namespace App\Services\Tag;

use Carbon\Laravel\ServiceProvider;

class TagServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(Repositories\TagRepository::class, function ($app) {
            return new Repositories\TagRepository();
        });
        $this->app->singleton(TagService::class, function ($app) {
            return new TagService($app->make(Repositories\TagRepository::class));
        });
    }
}
