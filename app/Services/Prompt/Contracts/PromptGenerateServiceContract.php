<?php

namespace App\Services\Prompt\Contracts;

use App\Services\ServiceContract;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\Prompt\PromptGenerateResult;
use AIGenerate\Models\User\User;

interface PromptGenerateServiceContract extends ServiceContract
{
    public function store(Prompt $prompt, User $user, array $attributes): PromptGenerate;

    public function callApi(PromptGenerate $promptGenerate, array $attributes): PromptGenerateResult;
}
