<?php

namespace App\Http\Controllers\Prompt;

use App\Events\Prompt\ShowPromptEvent;
use App\Exceptions\Prompt\NotEnabledException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Prompt\IndexPromptRequest;
use App\Http\Requests\Prompt\PreviewPromptRequest;
use App\Http\Requests\Prompt\StorePromptRequest;
use App\Http\Requests\Prompt\UpdatePromptTemplateRequest;
use App\Http\Resources\Prompt\PromptCategoryResources;
use App\Http\Resources\Prompt\PromptEngineResources;
use App\Http\Resources\Prompt\PromptGenerateLanguageResources;
use App\Http\Resources\Prompt\PromptGenerateToneResources;
use App\Http\Resources\Prompt\PromptGenerateWritingStyleResources;
use App\Http\Resources\Prompt\PromptPreviewResources;
use App\Http\Resources\Prompt\PromptResources;
use App\Http\Resources\Prompt\PromptShowForOwnerResources;
use App\Http\Resources\Prompt\PromptShowResources;
use App\Http\Resources\Prompt\PromptSortResources;
use App\Http\Resources\Prompt\PromptTypeResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Models\Prompt\Prompt;
use App\Services\Auth\Facades\AuthService;
use App\Services\Prompt\Contracts\PromptServiceContract;
use App\Services\Prompt\Sorts\Enums\Sorts;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;
use AIGenerate\Models\Prompt\Enums\Status;
use AIGenerate\Models\Prompt\PromptCategory;
use AIGenerate\Models\Prompt\PromptGenerateLanguage;
use AIGenerate\Models\Prompt\PromptGenerateTone;
use AIGenerate\Models\Prompt\PromptGenerateWritingStyle;
use AIGenerate\Models\Prompt\PromptType;

class PromptController extends BaseController
{

    public function __construct(private readonly PromptServiceContract $service)
    {
        $this->middleware('auth:api')->only(['store', 'updateTemplate', 'previewTemplate', 'destroy', 'showForOwner']);
        // $this->authorizeResource(Prompt::class, 'prompt');
    }

    /**
     * Prompt 상세 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/prompt/{prompt}",
     *     summary="prompt detail page api",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
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
     *               @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/PromptShowResources",
     *              ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function show(Prompt $prompt)
    {
        // throw_unless(Gate::allows('view-prompt', $prompt), new NotEnabledException());
        throw_if($prompt->prompt_status_code != Status::Enabled->value, new NotEnabledException());
        return $this->run(function () use ($prompt) {
            if (AuthService::check()) {
                $user = AuthService::currentUser();
                $generated = $this->service->generatedPrompts($user, $prompt);
                $isFavorite = $this->service->isFavorite($user, $prompt);
                ShowPromptEvent::dispatch($prompt, $user);
            } else {
                $isFavorite = false;
                $generated = collect();
            }
            $prompt->generated = $generated;
            $prompt->others = $this->service->otherPrompts($prompt);
            $prompt->new = $this->service->newPrompts();
            $prompt->popular = $this->service->popularPrompts();
            $prompt->isFavorite = $isFavorite;
            return ResponseTemplate::toJson(new PromptShowResources($prompt));
        });
    }

    /**
     * Prompt 상세 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/prompt/{prompt}/owner",
     *     summary="prompt detail page for owner api",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
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
     *               @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/PromptShowForOwnerResources",
     *              ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function showForOwner(Prompt $prompt)
    {
        return $this->run(function () use ($prompt) {
            $user = AuthService::currentUser();
            if ($user->can('showForOwner', $prompt)) {
                // $this->authorize('delete', $prompt);
                return ResponseTemplate::toJson(new PromptShowForOwnerResources($prompt));
            } else {
                return ResponseTemplate::toJson(false, '권한이 없습니다.', Response::HTTP_FORBIDDEN);
            }
        });
    }

    /**
     * Prompt 생성 페이지에서 사용될 종류, 엔진 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/prompt/create",
     *     summary="prompt create page api",
     *     tags={"prompt"},
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
     *               @OA\Property(
     *                 property="types",
     *                 type="array",
     *                 description="api types",
     *                 @OA\Items(ref="#/components/schemas/PromptTypeResources"),
     *                ),
     *                @OA\Property(
     *                 property="categories",
     *                 type="array",
     *                 description="categories",
     *                 @OA\Items(ref="#/components/schemas/PromptCategoryResources"),
     *                 ),
     *                ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function create()
    {
        return $this->run(function () {
            return ResponseTemplate::toJson([
                'types'      => $this->service->getTypes()->map(fn($item) => new PromptTypeResources($item)),
                'categories' => $this->service->getCategories()->map(fn($item) => new PromptCategoryResources($item)),
            ]);
        });
    }

    /**
     * Prompt 를 삭제합니다.
     * @OA\Delete(
     *     path="/api/prompt/{prompt}",
     *     summary="prompt delete api",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
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
     *               @OA\Property(
     *                 property="data",
     *                 type="object",
     *              ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function destroy(Prompt $prompt)
    {
        return $this->transaction(function () use ($prompt) {
            $user = AuthService::currentUser();
            if ($user->can('delete', $prompt)) {
                // $this->authorize('delete', $prompt);
                return ResponseTemplate::toJson($this->service->delete($prompt));
            } else {
                return ResponseTemplate::toJson(false, '권한이 없습니다.', Response::HTTP_FORBIDDEN);
            }
        });
    }

    /**
     * Prompt 를 생성 합니다.
     * @OA\Post(
     *     path="/api/prompt",
     *     summary="prompt store api",
     *     tags={"prompt"},
     *     @OA\RequestBody(
     *       @OA\MediaType(
     *         mediaType="multipart/form-data",
     *         @OA\Schema(ref="#/components/schemas/StorePromptRequest")
     *       )
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/PromptResources"),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function store(StorePromptRequest $request)
    {
        $validated = $request->validated();
        return $this->transaction(function () use ($validated) {
            $validated['user_id'] = AuthService::currentUser()->id;
            $prompt = $this->service->store($validated);
            return ResponseTemplate::toJson(new PromptResources($prompt));
        });
    }

    /**
     * Prompt 등록 후 prompt template 페이지 정보를 제공 합니다
     * 이전에 등록한 Prompt 의 type 이 image, chat, completion 에 따라 데이터가 변동 됩니다.
     * @OA\Get(
     *     path="/api/prompt/{prompt}/template",
     *     summary="prompt template page api",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
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
     *               @OA\Property(
     *                 property="data",
     *                 type="object",
     *               @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 description="type",
     *              ),
     *               @OA\Property(
     *                 property="engines",
     *                 type="array",
     *                 description="engines",
     *                 @OA\Items(ref="#/components/schemas/PromptEngineResources"),
     *                ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function showTemplate(Prompt $prompt)
    {
        return $this->run(function () use ($prompt) {
            return ResponseTemplate::toJson([
                'type'    => $prompt->prompt_type_code,
                'engines' => $this->service->getEngines($prompt)->map(fn($item) => new PromptEngineResources($item)),
            ]);
        });
    }

    /**
     * Prompt 미리보기 입니다.
     * @OA\Post(
     *     path="/api/prompt/{prompt}/template",
     *     summary="preview prompt template api",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="template",
     *         required=true,
     *         description="prompt template",
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="image template", value="Cute baby sea otter doing [PROMPT] the weather [WEATHER]", summary="image template"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="guide",
     *         required=true,
     *         description="prompt guide",
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="guide", value="guide for prompt", summary="guide"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="order",
     *         required=false,
     *         description="prompt last order",
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="last order", value="It is last order", summary="last order"),
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/PromptPreviewResources"),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function previewTemplate(Prompt $prompt, PreviewPromptRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($prompt, $validated) {
            $options = $this->service->matchOptions($validated['template']);
            unset($validated['template']);
            $validated['options'] = $options;
            return ResponseTemplate::toJson(new PromptPreviewResources($validated));
        });
    }

    /**
     * Prompt 등록 후 prompt template, guide 를 저장 합니다.
     * @OA\Put(
     *     path="/api/prompt/{prompt}/template",
     *     summary="update prompt template api",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="template",
     *         required=true,
     *         description="prompt template",
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="image template", value="Cute baby sea otter doing [PROMPT] the weather [WEATHER]", summary="image template"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="guide",
     *         required=true,
     *         description="prompt guide",
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="guide", value="guide for prompt", summary="guide"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="engine",
     *         required=false,
     *         description="prompt engine",
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="gpt-3.5-turbo", value="gpt-3.5-turbo", summary="gpt-3.5-turbo"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="order",
     *         required=false,
     *         description="prompt last order",
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="last order", value="It is last order", summary="last order"),
     *     ),
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/PromptResources"),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function updateTemplate(Prompt $prompt, UpdatePromptTemplateRequest $request)
    {
        $validated = $request->validated();
        return $this->transaction(function () use ($validated, $prompt) {
            return ResponseTemplate::toJson(new PromptResources($this->service->update($prompt, $validated)));
        });
    }

    /**
     * Prompt generate 시 필요한 language, tone, writing style 정보를 제공 합니다
     * @OA\Get(
     *     path="/api/prompt/input-options",
     *     summary="get prompt generate options api",
     *     tags={"prompt"},
     *      @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="A simple of response message",
     *              ),
     *             @OA\Property(
     *              property="data",
     *              type="object",
     *               @OA\Property(
     *                  property="language",
     *                  type="array",
     *                  description="language",
     *                  @OA\Items(ref="#/components/schemas/PromptGenerateLanguageResources"),
     *               ),
     *               @OA\Property(
     *                  property="tone",
     *                  type="array",
     *                  description="tone",
     *                  @OA\Items(ref="#/components/schemas/PromptGenerateToneResources"),
     *               ),
     *               @OA\Property(
     *                  property="writing_style",
     *                  type="array",
     *                  description="writing_style",
     *                  @OA\Items(ref="#/components/schemas/PromptGenerateWritingStyleResources"),
     *               ),
     *            ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function getInputOptions()
    {
        return $this->run(function () {
            return ResponseTemplate::toJson([
                'language'      => $this->service->getLanguages()->map(
                    fn($item) => new PromptGenerateLanguageResources($item),
                )->prepend(
                    new PromptGenerateLanguageResources(
                        new PromptGenerateLanguage([
                            'code' => null,
                            'name' => 'None',
                        ]),
                    ),
                ),
                'tone'          => $this->service->getTones()->map(fn($item) => new PromptGenerateToneResources($item),
                )->prepend(
                    new PromptGenerateToneResources(
                        new PromptGenerateTone([
                            'code' => null,
                            'name' => 'None',
                        ]),
                    ),
                ),
                'writing_style' => $this->service->getWritingStyles()->map(
                    fn($item) => new PromptGenerateWritingStyleResources($item),
                )->prepend(
                    new PromptGenerateWritingStyleResources(
                        new PromptGenerateWritingStyle([
                            'code' => null,
                            'name' => 'None',
                        ]),
                    ),
                ),
            ]);
        });
    }

    /**
     * Prompt 목록 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/prompt/main",
     *     summary="main prompt list",
     *     tags={"prompt"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="items",
     *                  type="array",
     *                  description="result array",
     *                  @OA\Items(ref="#/components/schemas/PromptResources"),
     *              ),
     *            ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function main()
    {
        return $this->run(function () {
            $result = $this->service->main()->map(fn($item) => new PromptResources($item));
            return ResponseTemplate::toJson($result);
        });
    }

    /**
     * Prompt 목록 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/prompt",
     *     summary="prompt list, search",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         description="search keyword in title, description, user email, name",
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         description="page size",
     *         in="query",
     *         name="size",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="page sort",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="newest", value="newest", summary="newest"),
     *         @OA\Examples(example="oldest", value="oldest", summary="oldest"),
     *     ),
     *     @OA\Parameter(
     *         description="types",
     *         in="query",
     *         name="types[]",
     *         required=false,
     *         @OA\Schema(type="array",
     *          @OA\Items(
     *         type="string",
     *         @OA\Examples(example="Image", value="image", summary="Image"),
     *         @OA\Examples(example="Chat", value="chat", summary="Chat"),
     *          ),
     *        ),
     *     ),
     *     @OA\Parameter(
     *         description="categories",
     *         in="query",
     *         name="categories[]",
     *         required=false,
     *         @OA\Schema(type="array",
     *          @OA\Items(
     *         type="string",
     *         @OA\Examples(example="3D", value="1", summary="3D"),
     *         @OA\Examples(example="Accessory", value="2", summary="Accessory"),
     *          ),
     * ),
     *     ),
     *     @OA\Parameter(
     *         description="page last item id",
     *         in="query",
     *         name="last",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="10", value="10", summary="10"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="items",
     *                  type="array",
     *                  description="result array",
     *                  @OA\Items(ref="#/components/schemas/PromptResources"),
     *              ),
     *            ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function index(IndexPromptRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $result = $this->service->index(
                $validated['search'] ?? null,
                Sorts::from($validated['sort'] ?? Sorts::Newest->value),
                $validated['page'] ?? 0,
                $validated['size'] ?? 10,
                $validated['last'] ?? 0,
                isset($validated['types']) ? collect($validated['types'])->filter()->toArray() : [],
                isset($validated['categories']) ? collect($validated['categories'])->filter()->toArray() : [],
            )->map(fn($item) => new PromptResources($item));
            return ResponseTemplate::toJson($result);
        });
    }

    /**
     * Prompt 생성 페이지에서 사용될 종류, 엔진 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/prompt/searchable-values",
     *     summary="prompt searchable data",
     *     tags={"prompt"},
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
     *               @OA\Property(
     *                 property="sorts",
     *                 type="array",
     *                 description="api sorts",
     *                 @OA\Items(ref="#/components/schemas/PromptSortResources"),
     *                ),
     *               @OA\Property(
     *                 property="types",
     *                 type="array",
     *                 description="api types",
     *                 @OA\Items(ref="#/components/schemas/PromptTypeResources"),
     *                ),
     *                @OA\Property(
     *                 property="categories",
     *                 type="array",
     *                 description="categories",
     *                 @OA\Items(ref="#/components/schemas/PromptCategoryResources"),
     *                 ),
     *                ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function searchableValues()
    {
        return $this->run(function () {
            return ResponseTemplate::toJson([
                'sorts'      => collect(Sorts::cases())->map(fn($item) => new PromptSortResources($item)),
                'types'      => $this->service->getTypes()
                                              ->map(fn($item) => new PromptTypeResources($item))
                                              ->prepend(
                                                  new PromptTypeResources(
                                                      new PromptType([
                                                          'id'   => null,
                                                          'name' => 'All',
                                                          'code' => null,
                                                      ]),
                                                  ),
                                              ),
                'categories' => $this->service->getCategories()
                                              ->map(fn($item) => new PromptCategoryResources($item))
                                              ->prepend(
                                                  new PromptCategoryResources(
                                                      new PromptCategory([
                                                          'id'   => null,
                                                          'name' => 'All',
                                                          'code' => null,
                                                      ]),
                                                  ),
                                              ),
            ]);
        });
    }
}
