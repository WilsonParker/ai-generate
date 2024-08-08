<?php

namespace App\Http\Controllers\Callback;

use App\Events\Stock\ImageToImageGeneratedEvent;
use App\Http\Repositories\Generate\ImageToImageRepository;

class ImageToImageGenerateController extends BaseGenerateController
{
    public function __construct(private ImageToImageRepository $repository) {}


    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getEventClass()
    {
        return ImageToImageGeneratedEvent::class;
    }
}
