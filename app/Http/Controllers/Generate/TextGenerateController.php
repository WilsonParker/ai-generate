<?php

namespace App\Http\Controllers\Generate;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Generate\IndexGenerateRequest;
use App\Http\Requests\Generate\IndexTextGenerateRequest;
use App\Http\Requests\Generate\TextGenerateStockRequest;
use App\Http\Resources\Generate\TextGenerateExportResources;
use App\Http\Resources\Generate\TextGenerateResources;
use App\Http\Response\Facades\Paginator;
use App\Http\Response\Facades\ResponseTemplate;
use App\Models\Generate\TextGenerate;
use App\Services\Auth\Facades\AuthService;
use Illuminate\Auth\AuthenticationException;
use OpenApi\Annotations as OA;
use AIGenerate\Models\Stock\Enums\Ethnicity;
use AIGenerate\Models\Stock\Enums\Gender;
use AIGenerate\Services\Generate\Contracts\TextGenerateServiceContract;
use AIGenerate\Services\Generate\Enums\Ratio;
use AIGenerate\Services\Generate\Enums\TextGenerateType;
use Throwable;

class TextGenerateController extends BaseController
{
    public function __construct(
        private readonly TextGenerateServiceContract $service,
    ) {
        $this->middleware('auth:api')->only(['index', 'destroy']);
        $this->middleware('text-generate')->only(['generate']);
    }

    /**
     * stock image 생성을 위해 image generate api 호출 후 callback
     * @OA\Post(
     *     path="/api/generate/text",
     *     summary="text generate",
     *     tags={"text", "generate"},
     *   @OA\Parameter(
     *        in="query",
     *        name="width",
     *        required=true,
     *        example="512",
     *        @OA\Schema(type="integer"),
     *     ),
     *   @OA\Parameter(
     *         in="query",
     *         name="height",
     *         required=true,
     *         example="512",
     *         @OA\Schema(type="integer"),
     *      ),
     *   @OA\Parameter(
     *         in="query",
     *         name="type",
     *         required=true,
     *         example="Portrait",
     *         @OA\Schema(type="string"),
     *      ),
     *   @OA\Parameter(
     *       in="query",
     *       name="ethnicity",
     *       required=false,
     *       description="ethnicity",
     *       example="caucasian",
     *       @OA\Schema(type="string"),
     *    ),
     *    @OA\Parameter(
     *       in="query",
     *       name="gender",
     *       required=false,
     *       description="gender",
     *       example="male",
     *      @OA\Schema(type="string"),
     *     ),
     *    @OA\Parameter(
     *        in="query",
     *        name="age",
     *        required=false,
     *        description="age",
     *        example="20",
     *        @OA\Schema(type="integer"),
     *      ),
     *    @OA\Parameter(
     *        in="query",
     *        name="skin_reality",
     *        required=false,
     *        description="skin_reality",
     *        example="true",
     *        @OA\Schema(type="bool"),
     *      ),
     *    @OA\Parameter(
     *         in="query",
     *         name="prompt",
     *         required=false,
     *         description="prompt",
     *         example="a car",
     *         @OA\Schema(type="string"),
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
    public function generate(TextGenerateStockRequest $request)
    {
        $validated = $request->validated();
        $callback = function () use ($validated) {
            $response = $this->service->generate(
                prompt       : $validated['prompt'],
                user         : AuthService::currentUser(),
                width        : $validated['width'],
                height       : $validated['height'],
                type         : TextGenerateType::tryFrom($validated['type']),
                ethnicity    : Ethnicity::tryFrom($validated['ethnicity'] ?? null),
                gender       : Gender::tryFrom($validated['gender'] ?? null),
                age          : $validated['age'] ?? null,
                isSkinReality: $validated['skin_reality'] ?? true,
                callbackUrl  : config('stock-generate.text_generate_callback_url'),
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
     *     path="/api/generate/text/forms",
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
     *                  property="gender",
     *                  type="array",
     *                  description="gender",
     *                  @OA\Items(
     *                    @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     ),
     *                     @OA\Property(
     *                      property="value",
     *                      type="string",
     *                     ),
     *                   ),
     *                  ),
     *              @OA\Property(
     *                  property="ethnicity",
     *                  type="array",
     *                  description="ethnicity",
     *                  @OA\Items(
     *                    @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     ),
     *                     @OA\Property(
     *                      property="value",
     *                      type="string",
     *                     ),
     *                   ),
     *                 ),
     *              @OA\Property(
     *                  property="ratio",
     *                  type="array",
     *                  description="ratio",
     *                  @OA\Items(
     *                    @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     ),
     *                     @OA\Property(
     *                      property="value",
     *                      type="string",
     *                     ),
     *                   ),
     *                 ),
     *              @OA\Property(
     *                  property="type",
     *                  type="array",
     *                  description="type",
     *                  @OA\Items(
     *                    @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     ),
     *                     @OA\Property(
     *                      property="value",
     *                      type="string",
     *                     ),
     *                   ),
     *                 ),
     *              ),
     *        ),
     *     ),
     * )
     *
     * @throws Throwable
     */
    public function forms()
    {
        return $this->run(function () {
            return ResponseTemplate::toJson(
                [
                    'gender'    => Gender::getEnumAttributes(value: 'code'),
                    'ethnicity' => Ethnicity::getEnumAttributes(value: 'code'),
                    'ratio'     => Ratio::getEnumAttributes(value: 'code'),
                    'type'      => TextGenerateType::getEnumAttributes(value: 'code'),
                ],
            );
        });
    }

    /**
     * 유저가 stock generate 한 모든 결과물을 리스트를 제공 합니다.
     * @OA\Get(
     *     path="/api/generate/text",
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
     *                 ref="#/components/schemas/TextGenerateResources",
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
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $generate = $this->service->index(AuthService::currentUser(), $validated['page'] ?? 1, $validated['size'] ?? 6);
            $generate->setCollection($generate->transform(fn($item) => new TextGenerateResources($item)));
            return Paginator::transfer($generate);
        });
    }

    /**
     * text generate 삭제
     * @OA\Delete(
     *     path="/api/generate/text/{textGenerate}",
     *     summary="delete text generate",
     *     tags={"text", "generate"},
     *     @OA\Parameter(
     *         in="path",
     *         name="textGenerate",
     *         description="textGenerate id",
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
    public function destroy(TextGenerate $generate)
    {
        $this->authorize('destroy', $generate);
        return $this->run(function () use ($generate) {
            return ResponseTemplate::toJson(
                message: $this->service->destroy($generate),
            );
        });
    }

    /**
     * text generate 된 결과물의 image url 제공
     * @OA\Get(
     *     path="/api/generate/text/{textGenerate}/export",
     *     summary="Provides image URL of text generated results",
     *     tags={"text", "generate"},
     *     @OA\Parameter(
     *         in="path",
     *         name="textGenerate",
     *         description="textGenerate id",
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
    public function exportImageUrl(TextGenerate $textGenerate)
    {
        return $this->run(function () use ($textGenerate) {
            throw_if($textGenerate->user_id !== AuthService::currentUser()->getKey(),
                AuthenticationException::class, 'You do not have permission to this result.');
            $export = $this->service->storeExport($textGenerate);
            $export->imageUrl = $textGenerate->images->first()?->getOriginalTemporaryUrl();
            return ResponseTemplate::toJson(new TextGenerateExportResources($export));
        });
    }
}
