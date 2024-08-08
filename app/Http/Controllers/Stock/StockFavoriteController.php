<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Stock\IndexStockFavoriteRequest;
use App\Http\Resources\Stock\StockFavoriteResources;
use App\Http\Resources\Stock\StockListResources;
use App\Http\Resources\Stock\StockResources;
use App\Http\Resources\Stock\StockUserFavoriteResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Services\Stock\Contracts\StockFavoriteServiceContract;

class StockFavoriteController extends BaseController
{
    public function __construct(private readonly StockFavoriteServiceContract $service)
    {
        $this->middleware('auth:api')->only(['store', 'delete', 'index']);
    }

    /**
     * 유저의 stock 즐겨찾기 목록 및 검색 기능을 제공합니다.
     * @OA\Get(
     *     path="/api/stock/favorite",
     *     summary="stock favorite list, search",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         description="page",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         @OA\Examples(example="1", value="1", summary="1"),
     *         @OA\Examples(example="2", value="2", summary="2"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/StockListResources"),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function index(IndexStockFavoriteRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $favorite = $this->service->index(AuthService::currentUser(), $validated['page'] ?? 1);
            $favorite->setCollection($favorite->transform(fn($item) => new StockListResources($item->stock)));
            return ResponseTemplate::toJson($favorite);
        });
    }

    /**
     * Stock 즐겨찾기를 등록합니다.
     * @OA\Post(
     *     path="/api/stock/favorite/{stock}",
     *     summary="store stock to favorite",
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
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/StockFavoriteResources"),
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
            return ResponseTemplate::toJson(new StockFavoriteResources($this->service->store($stock, AuthService::currentUser())));
        });
    }

    /**
     * Stock 즐겨찾기를 취소합니다.
     * @OA\Delete(
     *     path="/api/stock/favorite/{stock}",
     *     summary="delete stock to favorite",
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
