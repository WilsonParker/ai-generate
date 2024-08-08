<?php

namespace App\Http\Controllers\Prompt;

use App\Http\Controllers\BaseController;
use App\Services\Prompt\PromptGenerateService;

class PromptGenerateController extends BaseController
{

    public function __construct(private readonly PromptGenerateService $service)
    {
        $this->middleware('auth:api');
    }

}
