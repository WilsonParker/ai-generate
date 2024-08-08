<?php

namespace App\Http\Controllers\Generate;

use App\Http\Requests\Generate\IndexGenerateRequest;
use App\Http\Requests\Generate\TextToImageGenerateRequest;
use App\Http\Resources\Generate\TextToImageGenerateExportResources;
use App\Http\Resources\Generate\TextToImageGenerateResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Models\Generate\TextToImageGenerate;
use App\Services\Auth\Facades\AuthService;
use OpenApi\Annotations as OA;
use AIGenerate\Services\Generate\Contracts\TextToImageServiceContract;
use AIGenerate\Services\Generate\Enums\SamplingMethod;
use AIGenerate\Services\Generate\Enums\TextToImageType;
use Throwable;

class TextToImageGenerateController extends BaseGenerateController
{
    protected string $exportResourceClass = TextToImageGenerateExportResources::class;
    protected string $indexResourceClass = TextToImageGenerateResources::class;

    public function __construct(
        private readonly TextToImageServiceContract $service,
    ) {
        $this->middleware('auth:api')->only(['index', 'destroy', 'exportImageUrl']);
        $this->middleware('text-generate')->only(['generate']);
    }

    /**
     * txt2img 생성을 위해 image generate api 호출 후 callback
     * @OA\Post(
     *     path="/api/generate/txt2img",
     *     summary="text generate",
     *     tags={"text", "generate"},
     *     @OA\Parameter(
     *           description="prompt",
     *           in="query",
     *           name="prompt",
     *           required=true,
     *           @OA\Schema(type="string"),
     *       ),
     *      @OA\Parameter(
     *           description="negative",
     *           in="query",
     *           name="negative",
     *           required=false,
     *           @OA\Schema(type="string"),
     *       ),
     *      @OA\Parameter(
     *          description="width",
     *          in="query",
     *          name="width",
     *          required=false,
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="1024", value="1024", summary="1024"),
     *      ),
     *      @OA\Parameter(
     *          description="height",
     *          in="query",
     *          name="height",
     *          required=false,
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="1024", value="1024", summary="1024"),
     *      ),
     *      @OA\Parameter(
     *          description="sampling_method",
     *          in="query",
     *          name="sampling_method",
     *          required=false,
     *          @OA\Schema(type="string"),
     *          @OA\Examples(example="DPM++ 2M SDE Karras", value="DPM++ 2M SDE Karras", summary="DPM++ 2M SDE Karras"),
     *      ),
     *      @OA\Parameter(
     *          description="steps",
     *          in="query",
     *          name="steps",
     *          required=false,
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="30", value="30", summary="30"),
     *      ),
     *      @OA\Parameter(
     *          description="seed",
     *          in="query",
     *          name="seed",
     *          required=false,
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="-1", value="-1", summary="-1"),
     *      ),
     *      @OA\Parameter(
     *          description="denoising_strength",
     *          in="query",
     *          name="denoising_strength",
     *          required=false,
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="0.75", value="0.75", summary="0.75"),
     *      ),
     *      @OA\Parameter(
     *           description="alwayson_scripts",
     *           in="query",
     *           name="alwayson_scripts",
     *           required=false,
     *       ),
     *  @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *         ),
     *      ),
     * )
     *
     * @throws Throwable
     */
    public function generate(TextToImageGenerateRequest $request)
    {
        $validated = $request->validated();
        $callback = function () use ($validated) {
            $response = $this->service->generate(
                user        : AuthService::currentUser(),
                type        : TextToImageType::TEXT_TO_IMAGE,
                callbackUrl : config('stock-generate.text_to_image_generate_callback_url'),
                fillPrompt  : $validated['fill_prompt'] ?? false,
                prompt      : $validated['prompt'],
                fillNegative: $validated['fill_negative'] ?? false,
                negative    : $validated['negative'] ?? null,
                width       : $validated['width'] ?? 512,
                height      : $validated['height'] ?? 512,
                method      : isset($validated['sampling_method']) ? SamplingMethod::from($validated['sampling_method']) : SamplingMethod::DPM_PP_2M_SDE_KARRAS,
                steps       : $validated['steps'] ?? 20,
                cfgScale    : $validated['cfg_scale'] ?? 7.0,
                seed        : $validated['seed'] ?? -1,
                extension   : $validated['extension'] ?? [],
                additional  : $validated['additional'] ?? '',
            );
            return ResponseTemplate::toJson(
                message: $response,
            );
        };
        return $this->transaction($callback);
    }

    /**
     * Stock 리스트 페이지 필터 및 솔트 정보
     * @OA\Get(
     *     path="/api/generate/txt2img/forms",
     *     summary="text generate forms",
     *     tags={"text", "generate"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *               @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                  property="sampling_method",
     *                  type="array",
     *                  description="sampling_method",
     *                  @OA\Items( @OA\Property( type="string")),
     *                  ),
     *              @OA\Property(
     *                  property="lora",
     *                  type="array",
     *                  description="lora",
     *                  @OA\Items( @OA\Property( type="string")),
     *                 ),
     *              @OA\Property(
     *                  property="extension",
     *                  type="array",
     *                  description="extension",
     *                  @OA\Items( @OA\Property( type="string")),
     *                 ),
     *          ),
     *        ),
     *     ),
     * )
     *
     * @throws Throwable
     */
    public function forms()
    {
        return $this->baseForms();
    }

    /**
     * txt2img generate 된 결과물의 image url 제공
     * @OA\Get(
     *     path="/api/generate/txt2img/{generate}/export",
     *     summary="Provides image URL of text generated results",
     *     tags={"text", "generate"},
     *     @OA\Parameter(
     *         in="path",
     *         name="generate",
     *         description="generate id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *         @OA\Examples(example="2", value="2", summary="2"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *          @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/components/schemas/TextGenerateExportResources",
     *               ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function exportImageUrl(TextToImageGenerate $generate)
    {
        return $this->baseExportImageUrl($generate);
    }

    /**
     * 유저가 txt2img generate 한 모든 결과물을 리스트를 제공 합니다.
     * @OA\Get(
     *     path="/api/generate/txt2img",
     *     summary="",
     *     tags={"text", "generate"},
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *         @OA\Examples(example="2", value="2", summary="2"),
     *     ),
     *     @OA\Parameter(
     *          description="size",
     *          in="query",
     *          name="size",
     *          required=false,
     *          @OA\Schema(type="integer"),
     *          @OA\Examples(example="6", value="6", summary="6"),
     *          @OA\Examples(example="10", value="10", summary="10"),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *               @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/TextToImageGenerateResources",
     *              ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws Throwable
     */
    public function index(IndexGenerateRequest $request)
    {
        return $this->baseIndex($request->validated());
    }

    /**
     * txt2img generate 삭제
     * @OA\Delete(
     *     path="/api/generate/txt2img/{generate}",
     *     summary="delete text generate",
     *     tags={"text", "generate"},
     *     @OA\Parameter(
     *         in="path",
     *         name="generate",
     *         description="generate id",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *         @OA\Examples(example="2", value="2", summary="2"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function destroy(TextToImageGenerate $generate)
    {
        return $this->baseDestroy($generate);
    }

    protected function getService()
    {
        return $this->service;
    }
}
