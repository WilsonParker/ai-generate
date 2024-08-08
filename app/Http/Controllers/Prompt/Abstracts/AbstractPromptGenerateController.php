<?php

namespace App\Http\Controllers\Prompt\Abstracts;

use App\Events\Prompt\FreePromptGeneratedEvent;
use App\Events\Prompt\PromptGeneratedEvent;
use App\Http\Controllers\BaseController;
use App\Services\Mail\MailService;
use App\Services\Point\PointCalculator;
use App\Services\Prompt\Contracts\PromptGenerateServiceContract;
use App\Services\Prompt\FreeGenerateComposite\Contracts\CanGenerateForFree;
use App\Services\User\UserConstantService;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptGenerate;
use AIGenerate\Models\User\User;

class AbstractPromptGenerateController extends BaseController
{
    public function __construct(
        protected PromptGenerateServiceContract $service,
        protected PointCalculator $pointCalculator,
        protected CanGenerateForFree $canGenerateForFree,
        protected UserConstantService $userConstantService,
        protected MailService $mailService,
    ) {
        $this->setMiddleware();
    }

    protected function setMiddleware()
    {
        $this->middleware('auth:api');
    }

    public function expectPointAction(Prompt $prompt, array $validated)
    {
        $expectedTokens = $this->service->getExpectationTokens($prompt, $validated);
        return $this->pointCalculator->getExpectedPointToBePaid($prompt, $expectedTokens, $validated['size'] ?? null);
    }

    /**
     * @throws \Throwable
     */
    protected function storeAction(User $user, Prompt $prompt, array $validated): PromptGenerate
    {
        $expectedTokens = $this->service->getExpectationTokens($prompt, $validated);
        $promptGenerate = $this->service->store($prompt, $user, $validated);
        $callApi = function () use ($promptGenerate, $validated) {
            /**
             * @var \AIGenerate\Models\Prompt\PromptGenerateResult $promptGenerateResult
             * */
            $promptGenerateResult = $this->service->callApi($promptGenerate, $validated);
            $this->service->updatePromptGeneratePrice($promptGenerate, $promptGenerateResult);
        };

        if ($this->canGenerateForFree->isFree()) {
            $callApi();
            $this->userConstantService->incrementFreeGenerate($user);
            event(new FreePromptGeneratedEvent($promptGenerate));
            $this->mailService->dispatchFirstFreeGenerateCompletedEvent($user);
        } else {
            $this->pointCalculator->validatePointToBePaid($user, $prompt, $expectedTokens, $validated['size'] ?? null);
            $callApi();
            event(new PromptGeneratedEvent($promptGenerate));
            $this->mailService->dispatchFirstPaidGenerateEvent($user);
        }

        return $promptGenerate;
    }
}
