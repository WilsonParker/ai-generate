<?php

namespace App\Services\Prompt;

use App\Http\Repositories\OpenAI\OpenAiKeyRepository;
use App\Http\Repositories\Prompt\PromptGenerateResultRepository;
use App\Http\Repositories\RepositoryContract;
use App\Services\Prompt\Abstracts\AbstractPromptGptGenerateService;
use App\Services\Prompt\PromptDecorators\Decorators;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\User\User;
use AIGenerate\Services\AI\OpenAI\Abstracts\AbstractApiService;
use AIGenerate\Services\AI\OpenAI\Chat\ApiService;
use AIGenerate\Services\AI\OpenAI\Chat\BuildMessageTrait;

class PromptChatGenerateService extends AbstractPromptGptGenerateService
{
    use BuildMessageTrait;

    public function __construct(
        private readonly RepositoryContract $repository,
        private readonly PromptGenerateResultRepository $resultRepository,
        private readonly OpenAiKeyRepository $openAiKeyRepository,
        private readonly ApiService $apiService,
        private readonly Decorators $decorators,
    ) {}


    public function store(Prompt $prompt, User $user, array $attributes): PromptGenerate
    {
        $promptGenerate = $this->repository->create([
            'order'        => $attributes['order'] ?? null,
            'prompt_id'    => $prompt->getKey(),
            'user_id'      => $user->getKey(),
            'expired_at'   => Carbon::now()->addHours(2),
            'seller_price' => $prompt->price_per_generate,
            'max_tokens'   => $attributes['max_tokens'] ?? null,
            'image_size'   => null,
            'template'     => json_encode($this->buildInput($prompt, $attributes)),
        ]);

        $this->storeOption($prompt, $promptGenerate, $attributes);

        return $promptGenerate;
    }

    public function buildInput(Prompt $prompt, array $attributes): array
    {
        $template = $this->convertTemplate($prompt, $attributes);
        $messages[] = $this->buildMessage('system', $template);
        $prompt->filledOptions
            ->each(function ($option) use (&$messages) {
                $messages[] = $this->buildMessage($option->name, $option->value);
            });
        $order = $this->decorators->decorate('', $attributes);
        $messages[] = $this->buildMessage('user', $order);
        $attributes['model'] = $prompt->prompt_engine_code;
        $attributes['messages'] = $messages;
        return Arr::only($attributes, collect($this->apiService->getRules())->keys()->toArray());
    }

    protected function getTemplate(Prompt $prompt): string
    {
        return $prompt->template;
    }

    public function getExpectationTokens(Prompt $prompt, array $attributes): float
    {
        $input = $this->buildInput($prompt, $attributes);
        return count(gpt_encode(json_encode($input['messages']))) + $input['max_tokens'];
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
