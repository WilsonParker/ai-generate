<?php

namespace App\Http\Controllers\Generate;

use App\Http\Requests\Generate\ImageToImageGenerateRequest;
use App\Http\Requests\Generate\IndexGenerateRequest;
use App\Http\Resources\Generate\ImageToImageGenerateExportResources;
use App\Http\Resources\Generate\ImageToImageGenerateResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Models\Generate\ImageToImageGenerate;
use App\Services\Auth\Facades\AuthService;
use OpenApi\Annotations as OA;
use AIGenerate\Services\Generate\Contracts\ImageToImageServiceContract;
use AIGenerate\Services\Generate\Enums\ImageToImageType;
use AIGenerate\Services\Generate\Enums\SamplingMethod;
use Throwable;

class ImageToImageGenerateController extends BaseGenerateController
{
    protected string $exportResourceClass = ImageToImageGenerateExportResources::class;
    protected string $indexResourceClass = ImageToImageGenerateResources::class;

    public function __construct(
        private readonly imageToImageServiceContract $service,
    ) {
        $this->middleware('auth:api')->only(['index', 'destroy', 'exportImageUrl']);
        $this->middleware('image-generate')->only(['generate']);
    }

    /**
     * img2img 생성을 위해 image generate api 호출 후 callback
     * @OA\Post(
     *     path="/api/generate/img2img",
     *     summary="image generate",
     *     tags={"image", "generate"},
     *  @OA\RequestBody(
     *        @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/ImageToImageGenerateRequest")
     *        )
     *      ),
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
    public function generate(ImageToImageGenerateRequest $request)
    {
        $validated = $request->validated();
        $callback = function () use ($validated) {
            $response = $this->service->generate(
                user             : AuthService::currentUser(),
                type             : ImageToImageType::IMAGE_TO_IMAGE,
                callbackUrl      : config('stock-generate.image_to_image_generate_callback_url'),
                image            : $validated['image'],
                fillPrompt       : $validated['fill_prompt'] ?? false,
                prompt           : $validated['prompt'],
                fillNegative     : $validated['fill_negative'] ?? false,
                negative         : $validated['negative'],
                width            : $validated['width'] ?? 512,
                height           : $validated['height'] ?? 512,
                method           : isset($validated['sampling_method']) ? SamplingMethod::from($validated['sampling_method']) : SamplingMethod::DPM_PP_2M_SDE_KARRAS,
                steps            : $validated['steps'] ?? 20,
                cfgScale         : $validated['cfg_scale'] ?? 7.0,
                denoisingStrength: $validated['denoising_strength'] ?? 0.75,
                seed             : $validated['seed'] ?? -1,
                extension        : $validated['extension'] ?? [],
                additional       : $validated['additional'] ?? '',
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
     *     path="/api/generate/img2img/forms",
     *     summary="image generate forms",
     *     tags={"image", "generate"},
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
     * 유저가 img2img generate 한 모든 결과물을 리스트를 제공 합니다.
     * @OA\Get(
     *     path="/api/generate/img2img",
     *     summary="",
     *     tags={"image", "generate"},
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
     *                 ref="#/components/schemas/ImageToImageGenerateResources",
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
     * img2img generate 삭제
     * @OA\Delete(
     *     path="/api/generate/img2img/{generate}",
     *     summary="delete image generate",
     *     tags={"image", "generate"},
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
    public function destroy(ImageToImageGenerate $generate)
    {
        return $this->baseDestroy($generate);
    }

    /**
     * img2img generate 된 결과물의 image url 제공
     * @OA\Get(
     *     path="/api/generate/img2img/{generate}/export",
     *     summary="Provides image URL of image generated results",
     *     tags={"image", "generate"},
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
     *                  ref="#/components/schemas/ImageToImageGenerateExportResources",
     *               ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function exportImageUrl(ImageToImageGenerate $generate)
    {
        return $this->baseExportImageUrl($generate);
    }

    protected function getService()
    {
        return $this->service;
    }
}
