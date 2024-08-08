<?php

namespace App\Http\Controllers\Prompt;

use App\Http\Controllers\Prompt\Abstracts\AbstractPromptGenerateController;
use App\Http\Requests\Prompt\StoreImagePromptGenerateRequest;
use App\Http\Resources\Prompt\PromptGenerateResultResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use OpenApi\Annotations as OA;
use AIGenerate\Models\Prompt\Prompt;

class ImagePromptGenerateController extends AbstractPromptGenerateController
{
    /**
     * GPT DALL-E Prompt 를 generate 합니다
     * p1, p2 ... 의 정보는 판매자의 의해 변경이 되는 값 입니다.
     * 위 정보는 Prompt Generate 페이지 진입 시 서버에서 제공 받습니다.
     * @OA\Post(
     *     path="/api/prompt/{prompt}/generate/image",
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
     *         description="image size",
     *         in="query",
     *         name="size",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="256x256", value="256x256", summary="256x256"),
     *         @OA\Examples(example="512x512", value="512x512", summary="512x512"),
     *         @OA\Examples(example="1024x1024", value="1024x1024", summary="1024x1024"),
     *     ),
     *     @OA\Parameter(
     *         description="generate numbers example",
     *         in="query",
     *         name="n",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *     ),
     *     @OA\Parameter(
     *         description="weather value example",
     *         in="query",
     *         name="p1",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="sunny", value="sunny", summary="sunny"),
     *         @OA\Examples(example="wendy", value="wendy", summary="wendy"),
     *     ),
     *     @OA\Parameter(
     *         description="prompt value example",
     *         in="query",
     *         name="p2",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="banging clams", value="banging clams", summary="banging clams"),
     *         @OA\Examples(example="catching fish", value="catching fish", summary="catching fish"),
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
    public function store(Prompt $prompt, StoreImagePromptGenerateRequest $request)
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
     *     path="/api/prompt/{prompt}/generate/expeect/image",
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
     *         description="image size",
     *         in="query",
     *         name="size",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="256x256", value="256x256", summary="256x256"),
     *         @OA\Examples(example="512x512", value="512x512", summary="512x512"),
     *         @OA\Examples(example="1024x1024", value="1024x1024", summary="1024x1024"),
     *     ),
     *     @OA\Parameter(
     *         description="generate numbers example",
     *         in="query",
     *         name="n",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *     ),
     *     @OA\Parameter(
     *         description="weather value example",
     *         in="query",
     *         name="p1",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="sunny", value="sunny", summary="sunny"),
     *         @OA\Examples(example="wendy", value="wendy", summary="wendy"),
     *     ),
     *     @OA\Parameter(
     *         description="prompt value example",
     *         in="query",
     *         name="p2",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="banging clams", value="banging clams", summary="banging clams"),
     *         @OA\Examples(example="catching fish", value="catching fish", summary="catching fish"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="float", example="1.116"),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function expectPoint(Prompt $prompt, StoreImagePromptGenerateRequest $request)
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
