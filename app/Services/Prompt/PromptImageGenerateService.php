<?php

namespace App\Services\Prompt;

use App\Http\Repositories\OpenAI\OpenAiKeyRepository;
use App\Http\Repositories\Prompt\PromptGenerateResultRepository;
use App\Http\Repositories\RepositoryContract;
use App\Services\Prompt\Abstracts\AbstractPromptGptGenerateService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\User\User;
use AIGenerate\Services\AI\OpenAI\Abstracts\AbstractApiService;
use AIGenerate\Services\AI\OpenAI\Images\ApiService;

class PromptImageGenerateService extends AbstractPromptGptGenerateService
{
    public function __construct(
        private readonly RepositoryContract $repository,
        private readonly PromptGenerateResultRepository $resultRepository,
        private readonly OpenAiKeyRepository $openAiKeyRepository,
        private readonly ApiService $apiService,
    ) {}


    public function store(Prompt $prompt, User $user, array $attributes): PromptGenerate
    {
        $promptGenerate = $this->repository->create([
            'prompt_id'    => $prompt->getKey(),
            'user_id'      => $user->getKey(),
            'expired_at'   => Carbon::now()->addHours(2),
            'max_tokens'   => 1,
            'seller_price' => $prompt->price_per_generate,
            'image_size'   => $attributes['size'],
            'template'     => json_encode($this->buildInput($prompt, $attributes)),
        ]);

        $this->storeOption($prompt, $promptGenerate, $attributes);

        return $promptGenerate;
    }

    public function buildInput(Prompt $prompt, array $attributes): array
    {
        $template = $this->convertTemplate($prompt, $attributes);
        return array_merge(
            [
                'model'  => $prompt->prompt_engine_code,
                'prompt' => $template,
            ],
            Arr::only($attributes, collect($this->apiService->getRules())->keys()->toArray()),
        );
    }

    protected function getTemplate(Prompt $prompt): string
    {
        return $prompt->template;
    }

    public function getExpectationTokens(Prompt $prompt, array $attributes): float
    {
        return 1;
    }

    protected function getRepository(): RepositoryContract
    {
        return $this->repository;
    }

    protected function getApiService(): AbstractApiService
    {
        return $this->apiService;
    }

    protected function getResultRepository(): PromptGenerateResultRepository
    {
        return $this->resultRepository;
    }

    protected function getOpenAiKeyRepository(): OpenAiKeyRepository
    {
        return $this->openAiKeyRepository;
    }
}
