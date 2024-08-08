<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Http\Requests\User\IndexPromptGeneratedRequest;
use App\Http\Requests\User\IndexPromptGenerateRequest;
use App\Http\Resources\Prompt\PromptGenerateIndexResources;
use App\Http\Resources\Prompt\PromptResources;
use App\Http\Resources\Prompt\SellerPromptResources;
use App\Http\Response\Facades\Paginator;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use App\Services\Prompt\PromptGenerateService;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class PromptController extends BaseController
{
    public function __construct(private readonly PromptGenerateService $service)
    {
        $this->middleware('auth:api');
    }

    /**
     * Prompt Generate 한 목록 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/user/prompt/list/generate",
     *     summary="prompt generated list",
     *     tags={"user"},
     *     @OA\Parameter(
     *         description="page size",
     *         in="query",
     *         name="size",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
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
     *                  @OA\Items(ref="#/components/schemas/PromptGenerateIndexResources"),
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
    public function generateList(IndexPromptGenerateRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $paginator = $this->service->getGenerateList(
                AuthService::currentUser(),
                $validated['page'] ?? 0,
                $validated['size'] ?? 10,
            );

            $paginator->transform(function ($item) {
                return new PromptGenerateIndexResources($item);
            });
            return ResponseTemplate::toJson(Paginator::transfer($paginator));
        });
    }

    /**
     * Prompt 생성 한 목록 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/user/prompt/list/create",
     *     summary="prompt create list",
     *     tags={"user"},
     *     @OA\Parameter(
     *         description="page size",
     *         in="query",
     *         name="size",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
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
     *                  @OA\Items(ref="#/components/schemas/PromptResources"),
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
    public function createList(IndexPromptGenerateRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $paginator = $this->service->getCreateList(
                AuthService::currentUser(),
                $validated['page'] ?? 0,
                $validated['size'] ?? 10,
            );

            $paginator->transform(function ($item) {
                return new PromptResources($item);
            });
            return ResponseTemplate::toJson(Paginator::transfer($paginator));
        });
    }

    /**
     * 등록한 Prompt 가 Generate 된 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/user/prompt/list/selling",
     *     summary="prompt generate selling list",
     *     tags={"user"},
     *     @OA\Parameter(
     *         description="page size",
     *         in="query",
     *         name="size",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="start",
     *         in="query",
     *         name="start",
     *         required=false,
     *         example="2023-01-01",
     *         @OA\Schema(type="date"),
     *     ),
     *     @OA\Parameter(
     *         description="end",
     *         in="query",
     *         name="end",
     *         required=false,
     *         example="2023-12-31",
     *         @OA\Schema(type="date"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *          @OA\JsonContent(
     *              type="object",
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
     *                  @OA\Items(ref="#/components/schemas/SellerPromptResources"),
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
    public function sellingList(IndexPromptGeneratedRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $user = AuthService::currentUser();
            $start = isset($validated['start']) ? Carbon::createFromFormat('Y-m-d', $validated['start']) : null;
            $end = isset($validated['end']) ? Carbon::createFromFormat('Y-m-d', $validated['end']) : null;
            $paginator = $this->service->getSellerPromptGeneratedGroupByPrompt(
                $user,
                $start,
                $end,
                $validated
            );

            $paginator->transform(function ($item) {
                $item->revenue = $this->service->calculateSalesPrice($item->generates->sum('seller_price'));
                $item->revenue_per_price = $this->service->calculateSalesPrice($item->price_per_generate);
                return new SellerPromptResources($item);
            });
            $result = Paginator::transfer($paginator)->toArray();
            $result['total_revenue'] = $this->service->getTotalSalesPrice($user);

            $thisMonthFirst = now()->firstOfMonth();
            $thisMonthLast = now()->lastOfMonth();
            $result['waiting_revenue'] = $this->service->getSalesPrice($user, $thisMonthFirst, $thisMonthLast);
            return ResponseTemplate::toJson($result);
        });
    }
}
