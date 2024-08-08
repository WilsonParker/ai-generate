<?php

namespace App\Http\Controllers\Callback;

use App\Events\Stock\TextToImageGeneratedEvent;
use App\Http\Repositories\Generate\TextToImageRepository;

class TextToImageGenerateController extends BaseGenerateController
{
    public function __construct(private TextToImageRepository $repository) {}

    protected function getRepository()
    {
        return $this->repository;
    }

    protected function getEventClass()
    {
        return TextToImageGeneratedEvent::class;
    }

}
