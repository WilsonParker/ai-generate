<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Point\IndexPointRequest;
use App\Http\Resources\Point\PointHistoryResources;
use App\Http\Response\Facades\Paginator;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use App\Services\Point\PointService;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class PointController extends BaseController
{
    public function __construct(private readonly PointService $service)
    {
        $this->middleware('auth:api');
    }

    /**
     * Prompt Generate 한 목록 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/user/point",
     *     summary="point history list",
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
     *                  @OA\Items(ref="#/components/schemas/PointHistoryResources"),
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
    public function index(IndexPointRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $paginator = $this->service->index(
                AuthService::currentUser(),
                $validated['page'] ?? 0,
                $validated['size'] ?? 10,
                isset($validated['start']) ? Carbon::createFromFormat('Y-m-d', $validated['start']) : null,
                isset($validated['end']) ? Carbon::createFromFormat('Y-m-d', $validated['end']) : null,
            );

            $paginator->transform(function ($item) {
                return new PointHistoryResources($item);
            });
            return ResponseTemplate::toJson(Paginator::transfer($paginator));
        });
    }

}
