<?php

namespace App\Http\Controllers\Prompt;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Prompt\IndexPromptFavoriteRequest;
use App\Http\Resources\Prompt\PromptFavoriteResources;
use App\Http\Response\Facades\Paginator;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use App\Services\Prompt\PromptFavoriteService;
use App\Services\Prompt\Sorts\Enums\Sorts;
use OpenApi\Annotations as OA;
use AIGenerate\Models\Prompt\Prompt;

class PromptFavoriteController extends BaseController
{

    public function __construct(private readonly PromptFavoriteService $service)
    {
        $this->middleware('auth:api');
    }

    /**
     * Prompt 즐겨찾기 목록 및 검색 기능을 제공합니다.
     * @OA\Get(
     *     path="/api/prompt/favorite",
     *     summary="prompt favorite list, search",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         description="page number",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="page size",
     *         in="query",
     *         name="size",
     *         required=false,
     *         @OA\Schema(type="integer"),
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
     *              @OA\Property(
     *                 property="current_page",
     *                 type="integer",
     *                 description="current page",
     *                 example="1"
     *              ),
     *              @OA\Property(
     *                  property="items",
     *                  type="array",
     *                  description="result array",
     *                  @OA\Items(ref="#/components/schemas/PromptFavoriteResources"),
     *              ),
     *              @OA\Property(
     *                 property="last_page",
     *                 type="integer",
     *                 description="last page",
     *                 example="100"
     *              ),
     *              @OA\Property(
     *                 property="per_page",
     *                 type="integer",
     *                 description="page size",
     *                 example="10"
     *              ),
     *              @OA\Property(
     *                 property="from",
     *                 type="integer",
     *                 description="from item id",
     *                 example="1"
     *              ),
     *              @OA\Property(
     *                 property="to",
     *                 type="integer",
     *                 description="to item id",
     *                 example="10"
     *              ),
     *              @OA\Property(
     *                 property="total",
     *                 type="integer",
     *                 description="total size",
     *                 example="100"
     *              ),
     *            ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function index(IndexPromptFavoriteRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $paginator = $this->service->index(
                AuthService::currentUser(),
                Sorts::from($validated['sort'] ?? Sorts::Newest->value),
                $validated['page'] ?? 0,
                $validated['size'] ?? 10,
            );
            $paginator->transform(function ($item) {
                return new PromptFavoriteResources($item);
            });
            return ResponseTemplate::toJson(Paginator::transfer($paginator));
        });
    }

    /**
     * Prompt 즐겨찾기를 제거합니다.
     * @OA\Delete(
     *     path="/api/prompt/favorite/{prompt}",
     *     summary="delete prompt to favorite",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
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
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function delete(Prompt $prompt)
    {
        return $this->transaction(function () use ($prompt) {
            $result = $this->service->deleteFromModel(AuthService::currentUser(), $prompt);
            return ResponseTemplate::toJson($result);
        });
    }

    /**
     * Prompt 즐겨찾기를 추가합니다.
     * @OA\Post(
     *     path="/api/prompt/favorite/{prompt}",
     *     summary="add prompt to favorite",
     *     tags={"prompt"},
     *     @OA\Parameter(
     *         in="path",
     *         name="prompt",
     *         required=true,
     *         description="prompt id",
     *         example="1",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/PromptFavoriteResources"),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function store(Prompt $prompt)
    {
        return $this->transaction(function () use ($prompt) {
            return ResponseTemplate::toJson(
                new PromptFavoriteResources($this->service->store($prompt, AuthService::currentUser())),
            );
        });
    }
}
