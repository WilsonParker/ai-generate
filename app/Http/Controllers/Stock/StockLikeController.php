<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Stock\StockLikeResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Services\Stock\Contracts\StockLikeServiceContract;

class StockLikeController extends BaseController
{
    public function __construct(private readonly StockLikeServiceContract $service)
    {
        $this->middleware('auth:api')->only(['store', 'delete']);
    }

    /**
     * Stock 좋아요를 등록합니다.
     * @OA\Post(
     *     path="/api/stock/like/{stock}",
     *     summary="store stock to like",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         in="path",
     *         name="stock",
     *         required=true,
     *         description="stock id",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/StockLikeResources"),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function store(Stock $stock)
    {
        return $this->transaction(function () use ($stock) {
            return ResponseTemplate::toJson(new StockLikeResources($this->service->store($stock, AuthService::currentUser())));
        });
    }

    /**
     * Stock 좋아요를 취소합니다.
     * @OA\Delete(
     *     path="/api/stock/like/{stock}",
     *     summary="delete stock to like",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         in="path",
     *         name="stock",
     *         required=true,
     *         description="stock id",
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
     *               @OA\Property(
     *                 property="data",
     *                 type="boolean",
     *                 example=true,
     *              ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function delete(Stock $stock)
    {
        return $this->transaction(function () use ($stock) {
            return ResponseTemplate::toJson($this->service->delete($stock, AuthService::currentUser()));
        });
    }

}
