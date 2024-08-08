<?php

namespace App\Http\Controllers\Stock;

use App\Exceptions\Stock\NotEnabledException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Stock\DestroyStockReviewRequest;
use App\Http\Requests\Stock\StoreStockReviewRequest;
use App\Http\Resources\Stock\StockReviewResources;
use App\Http\Resources\Stock\StockSelectableReviewResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use AIGenerate\Models\Stock\Enums\ReviewTypes;
use AIGenerate\Models\Stock\Enums\Status;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Services\Stock\Contracts\StockReviewServiceContract;

class StockReviewController extends BaseController
{
    public function __construct(private readonly StockReviewServiceContract $service)
    {
        $this->middleware('auth:api')->only(['store', 'delete']);
    }

    /**
     * Stock 리뷰를 등록합니다.
     * @OA\Post(
     *     path="/api/stock/review/{stock}",
     *     summary="stock review store",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         in="path",
     *         name="stock",
     *         required=true,
     *         description="stock id",
     *         example="1",
     *     ),
     * @OA\RequestBody(
     *     description="Store stock review body",
     *     required=true,
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"type"},
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     description="type",
     *                     example="amazing"
     *                 ),
     *                 @OA\Property(
     *                     property="memo",
     *                     type="string",
     *                     description="memo",
     *                     example="Blah blah blah blah"
     *                 )
     *             )
     *         )
     *     }
     * ) ,
     * @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/StockReviewResources"),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function store(Stock $stock, StoreStockReviewRequest $request)
    {
        throw_if($stock->stock_status_code != Status::Enabled->value, new NotEnabledException());
        $validated = $request->validated();
        return $this->transaction(function () use ($stock, $validated) {
            return ResponseTemplate::toJson(new StockReviewResources($this->service->store($stock, AuthService::currentUser(), $validated)));
        });
    }

    /**
     * Stock review 등록을 취소합니다.
     * @OA\Delete(
     *     path="/api/stock/review/{stock}",
     *     summary="stock review delete",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         in="path",
     *         name="stock",
     *         required=true,
     *         description="stock id",
     *         example="1",
     *     ),
     * @OA\RequestBody(
     *     description="Delete stock review body",
     *     required=true,
     *     content={
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"type"},
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     description="type",
     *                     example="weird_face"
     *                 ),
     *             )
     *         )
     *     }
     * ) ,
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
    public function delete(Stock $stock, DestroyStockReviewRequest $request)
    {
        $validated = $request->validated();
        return $this->transaction(function () use ($stock, $validated) {
            return ResponseTemplate::toJson($this->service->delete($stock, AuthService::currentUser(), $validated['type']));
        });
    }

    /**
     * Stock review 선택 가능한 목록
     * @OA\Get(
     *     path="/api/stock/review/selectable-reviews",
     *     summary="stock review selectable data",
     *     tags={"stock"},
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
     *                 ref="#/components/schemas/StockSelectableReviewResources",
     *              ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws \Throwable
     */
    public function selectableReviews()
    {
        return $this->run(function () {
            return ResponseTemplate::toJson(
                collect(ReviewTypes::cases())->filter(fn($item) => $item->value !== ReviewTypes::from('feedback')->value)
                                             ->map(fn($item) => new StockSelectableReviewResources($item)),
            );
        });
    }

}
