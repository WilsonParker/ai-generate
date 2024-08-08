<?php

namespace App\Http\Controllers\Generate;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Generate\PluginTextGenerateStockRequest;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use AIGenerate\Models\Stock\Enums\Ethnicity;
use AIGenerate\Models\Stock\Enums\Gender;
use AIGenerate\Services\Generate\Contracts\TextGenerateServiceContract;
use AIGenerate\Services\Generate\Enums\TextGenerateType;

class PluginTextGenerateController extends BaseController
{
    public function __construct(
        private readonly TextGenerateServiceContract $service,
    ) {
        $this->middleware('auth:api')->only(['index']);
    }

    public function generate(PluginTextGenerateStockRequest $request)
    {
        $validated = $request->validated();
        $callback = function () use ($validated) {
            $response = $this->service->generate(
                prompt       : $validated['prompt'],
                user         : AuthService::pluginUser(),
                width        : $validated['width'],
                height       : $validated['height'],
                type         : TextGenerateType::tryFrom($validated['type']),
                ethnicity    : Ethnicity::tryFrom($validated['ethnicity'] ?? null),
                gender       : Gender::tryFrom($validated['gender'] ?? null),
                age          : $validated['age'] ?? null,
                isSkinReality: $validated['skin_reality'] ?? true,
                callbackUrl  : config('stock-generate.plugin_text_generate_callback_url'),
                additional   : $validated['hook_key'] ?? '',
            );
            return ResponseTemplate::toJson(
                message: $response,
            );
        };
        return $this->transaction($callback);
    }

}
