<?php

namespace App\Http\Controllers\Callback;

use App\Events\Stock\PluginTextGeneratedEvent;

class PluginTextGenerateController extends TextGenerateController
{

    protected function eventComplete($generate, array $payload): void
    {
        PluginTextGeneratedEvent::dispatch($generate, $payload['additional'] ?? '');
    }

}
