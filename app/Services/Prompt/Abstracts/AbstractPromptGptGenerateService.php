<?php

namespace App\Services\Prompt\Abstracts;

use App\Http\Repositories\OpenAI\OpenAiKeyRepository;
use App\Http\Repositories\Prompt\PromptGenerateResultRepository;
use App\Http\Repositories\RepositoryContract;
use App\Services\Prompt\Contracts\PromptGenerateServiceContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use OpenAI\Exceptions\ErrorException;
use AIGenerate\Models\OpenAI\OpenAiKeyStack;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\Prompt\PromptGenerateResult;
use AIGenerate\Services\AI\OpenAI\Abstracts\AbstractApiService;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;
use Throwable;

abstract class AbstractPromptGptGenerateService implements PromptGenerateServiceContract
{
    public function convertTemplate(Prompt|PromptGenerate $prompt, array $attributes = []): string
    {
        if ($prompt instanceof Prompt) {
            $template = $this->getTemplate($prompt);
            $prompt->fillableOptions->each(function ($option) use (&$template, $attributes) {
                $template = Str::of($template)->replace("[$option->name]", $attributes["p{$option->getKey()}"], false);
            });
        } else {
            $template = $prompt->prompt->template;
            $prompt->options->each(function ($option) use (&$template) {
                $template = Str::of($template)->replace("[{$option->option->name}]", $option->value, false);
            });
        }
        return $template;
    }

    abstract protected function getTemplate(Prompt $prompt): string;

    abstract public function getExpectationTokens(Prompt $prompt, array $attributes): float;

    public function updatePromptGeneratePrice(
        PromptGenerate $promptGenerate,
        PromptGenerateResult $promptGenerateResult,
        array $attributes = [],
    ): void {
        if ($promptGenerate->prompt->prompt_type_code == OpenAITypes::Image->value) {
            $inputPrice = 0;
            $outputPrice = $promptGenerate->prompt->getPointPerToken($promptGenerate->image_size);
        } else {
            $inputPrice = $promptGenerateResult->getPromptToken() * $promptGenerate->getPointPerToken();
            $outputPrice = ($attributes['max_tokens'] ?? $promptGenerateResult->getCompletionToken()) * $promptGenerate->getPointPerToken();
        }
        $this->getRepository()->update($promptGenerate, [
            'price'        => $promptGenerate->seller_price + $inputPrice + $outputPrice,
            'input_price'  => $inputPrice,
            'output_price' => $outputPrice,
        ]);
    }

    abstract protected function getRepository(): RepositoryContract;

    /**
     * @throws \Throwable
     */
    public function callApi(PromptGenerate $promptGenerate, array $attributes = []): PromptGenerateResult
    {
        return $this->callApiRecursive($promptGenerate);
    }

    public function callApiRecursive(
        PromptGenerate $promptGenerate,
        ?OpenAiKeyStack $key = null,
        ?int $try = 0,
    ): PromptGenerateResult {
        $keyModel = $this->changeApiKey($key);
        try {
            $result = $this->getApiService()->call(
                json_decode($promptGenerate->template, true),
                $keyModel->getApiKey(),
            );
            $date = $this->getCreatedDate($result['created']);
            return $this->getResultRepository()->createForPromptGenerate($promptGenerate, json_encode($result), $date);
        } catch (Throwable $throwable) {
            if ($try < 5
                && $throwable instanceof ErrorException
                && $throwable->getErrorType() == 'insufficient_quota') {
                return $this->callApiRecursive($promptGenerate, $keyModel, $try + 1);
            } else {
                throw $throwable;
            }
        } finally {
            $this->getOpenAiKeyRepository()->incrementCall($keyModel->getKey());
        }
    }

    protected function changeApiKey(?OpenAiKeyStack $model = null): OpenAiKeyStack
    {
        $date = now()->format('Y-m-d');
        $repository = $this->getOpenAiKeyRepository();
        $model = $repository->getOpenAiKeyStackModel($date, $model);
        Config::set('openai.api_key', $model->getKey());
        return $model;
    }

    abstract protected function getOpenAiKeyRepository(): OpenAiKeyRepository;

    abstract protected function getApiService(): AbstractApiService;

    protected function getCreatedDate($created): Carbon
    {
        return Carbon::createFromTimestamp($created);
    }

    abstract protected function getResultRepository(): PromptGenerateResultRepository;

    abstract public function buildInput(Prompt $prompt, array $attributes): array;

    protected function storeOption(Prompt $prompt, PromptGenerate $promptGenerate, array $attributes): void
    {
        $prompt->fillableOptions->each(function ($option) use ($promptGenerate, $attributes) {
            $promptGenerate->options()->create([
                'value'            => $attributes["p{$option->getKey()}"],
                'prompt_option_id' => $option->getKey(),
            ]);
        });
    }
}
