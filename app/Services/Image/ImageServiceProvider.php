<?php

namespace App\Services\Image;

use Carbon\Laravel\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(Contracts\ImageableRepository::class, function ($app) {
            return new Repositories\MediaRepository($app->make(Models\Media::class));
        });
        $this->app->singleton(Contracts\ImageServiceContract::class, function ($app) {
            return new MediaService(
                $app->make(Contracts\ImageableRepository::class),
                config('filesystems.default'),
                config('filesystems.path'),
            );
        });
    }
}
