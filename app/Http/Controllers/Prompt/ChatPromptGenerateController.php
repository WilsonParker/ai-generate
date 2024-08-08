<?php

namespace App\Http\Controllers\Prompt;

use AIGenerate\Models\Prompt\Prompt;
use App\Http\Controllers\Prompt\Abstracts\AbstractPromptGenerateController;
use App\Http\Requests\Prompt\StoreChatPromptGenerateRequest;
use App\Http\Resources\Prompt\PromptGenerateResultResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use OpenApi\Annotations as OA;

class ChatPromptGenerateController extends AbstractPromptGenerateController
{

    /**
     * GPT Chat Prompt 를 generate 합니다
     * @OA\Post(
     *     path="/api/prompt/{prompt}/generate/chat",
     *     summary="Adds a new prompt generate",
     *     tags={"prompt generate"},
     *     @OA\Parameter(
     *         description="prompt id example",
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *     ),
     *     @OA\Parameter(
     *         description="order message example",
     *         in="query",
     *         name="order",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="Where was it played?", value="Where was it played?", summary="Where was it played?"),
     *     ),
     *     @OA\Parameter(
     *         description="max tokens example",
     *         in="query",
     *         name="max_tokens",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="2048", value="2048", summary="2048"),
     *     ),
     *     @OA\Parameter(
     *         description="language",
     *         in="query",
     *         name="language",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="english", value="english?", summary="english"),
     *     ),
     *     @OA\Parameter(
     *         description="tone",
     *         in="query",
     *         name="tone",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="cold", value="cold?", summary="cold"),
     *     ),
     *     @OA\Parameter(
     *         description="writing style",
     *         in="query",
     *         name="writing_style",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="descriptive", value="descriptive?", summary="descriptive"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/PromptGenerateResultResources"),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function store(Prompt $prompt, StoreChatPromptGenerateRequest $request)
    {
        $validated = $request->validated();
        $callback = function () use ($validated, $prompt) {
            $promptGenerate = $this->storeAction(AuthService::currentUser(), $prompt, $validated);
            return ResponseTemplate::toJson(
                new PromptGenerateResultResources($promptGenerate),
                'prompt generate store success',
            );
        };
        return $this->transaction($callback);
    }

    /**
     * generate 시 지불 해야하는 예상 point 값을 제공합니다.
     * @OA\Post(
     *     path="/api/prompt/{prompt}/generate/expect/chat",
     *     summary="예상 generate 비용",
     *     tags={"prompt generate"},
     *     @OA\Parameter(
     *         description="prompt id example",
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *     ),
     *     @OA\Parameter(
     *         description="order message example",
     *         in="query",
     *         name="order",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="Where was it played?", value="Where was it played?", summary="Where was it played?"),
     *     ),
     *     @OA\Parameter(
     *         description="max tokens example",
     *         in="query",
     *         name="max_tokens",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="2048", value="2048", summary="2048"),
     *     ),
     *     @OA\Parameter(
     *         description="language",
     *         in="query",
     *         name="language",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="english", value="english?", summary="english"),
     *     ),
     *     @OA\Parameter(
     *         description="tone",
     *         in="query",
     *         name="tone",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="cold", value="cold?", summary="cold"),
     *     ),
     *     @OA\Parameter(
     *         description="writing style",
     *         in="query",
     *         name="writing_style",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="descriptive", value="descriptive?", summary="descriptive"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="float", example="1.1001328125"),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function expectPoint(Prompt $prompt, StoreChatPromptGenerateRequest $request)
    {
        $validated = $request->validated();
        $callback = function () use ($validated, $prompt) {
            return ResponseTemplate::toJson(
                $this->expectPointAction($prompt, $validated),
                'prompt generate expect point success',
            );
        };
        return $this->run($callback);
    }
}
