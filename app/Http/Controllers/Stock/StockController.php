<?php

namespace App\Http\Controllers\Stock;

use App\Events\Stock\ShowStockEvent;
use App\Exceptions\Stock\NotEnabledException;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Stock\GenerateStockRequest;
use App\Http\Requests\Stock\IndexStockRequest;
use App\Http\Requests\Stock\ShowStockRequest;
use App\Http\Resources\Stock\StockFilterEthnicityResources;
use App\Http\Resources\Stock\StockFilterGenderResources;
use App\Http\Resources\Stock\StockListCountResources;
use App\Http\Resources\Stock\StockShowResources;
use App\Http\Resources\Stock\StockSimilarResources;
use App\Http\Resources\Stock\StockSortResources;
use App\Http\Response\Facades\ResponseTemplate;
use App\Http\Response\Facades\SimplePaginator;
use App\Services\Auth\Facades\AuthService;
use OpenApi\Annotations as OA;
use AIGenerate\Models\Stock\Enums\Status;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Services\Stock\Contracts\StockRecommendServiceContract;
use AIGenerate\Services\Stock\Contracts\StockServiceContract;
use AIGenerate\Services\Stock\Sorts\Enums\Sorts;
use Throwable;

class StockController extends BaseController
{
    public function __construct(
        private readonly StockServiceContract $service,
        private readonly StockRecommendServiceContract $recommendService,
    ) {}

    /**
     * Stock 상세 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/stock/{stock}",
     *     summary="stock detail page api",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         in="path",
     *         name="stock",
     *         required=true,
     *         description="stock id",
     *         example="1",
     *     ),
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
     *                 ref="#/components/schemas/StockShowResources",
     *              ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws Throwable
     */
    public function show(Stock $stock, ShowStockRequest $request)
    {
        throw_if($stock->stock_status_code != Status::Enabled->value, new NotEnabledException());
        $validated = $request->validated();
        return $this->run(function () use ($stock, $validated) {
            if (AuthService::check()) {
                $user = AuthService::currentUser();
                $isLike = $this->service->isLike($stock, $user);
                $isFavorite = $this->service->isFavorite($stock, $user);
                $generateList = $this->service->getGenerates($stock, $user, $validated);
                $hasReview = $this->service->hasReview($stock, $user);
                ShowStockEvent::dispatch($stock, $user);
            } else {
                $isLike = false;
                $isFavorite = false;
                $generateList = null;
                $hasReview = collect();
            }

            $stock->isLike = $isLike;
            $stock->isFavorite = $isFavorite;
            $stock->generateList = $generateList;
            $stock->reviewList = $hasReview;
            return ResponseTemplate::toJson(new StockShowResources($stock));
        });
    }

    /**
     * Stock similar 목록 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/stock/{stock}/similar",
     *     summary="stock similar list api",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         in="path",
     *         name="stock",
     *         required=true,
     *         description="stock id",
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
     *              @OA\Property(
     *                  property="similar",
     *                  type="array",
     *                  description="similar stock information",
     *                  @OA\Items(ref="#/components/schemas/StockSimilarResources"),
     *               ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws Throwable
     */
    public function similar(Stock $stock, ShowStockRequest $request)
    {
        throw_if($stock->stock_status_code != Status::Enabled->value, new NotEnabledException());
        return $this->run(function () use ($stock) {
            $similar = $this->service->similar(
                $stock->information->gender,
                $stock->information->ethnicity,
            );
            $result = collect($similar->items())->map(fn($item) => new StockSimilarResources($item));
            return ResponseTemplate::toJson($result);
        });
    }


    /**
     * Stock 목록 정보를 제공 합니다.
     * @OA\Get(
     *     path="/api/stock",
     *     summary="stock list, search",
     *     tags={"stock"},
     *     @OA\Parameter(
     *         description="search keyword in title, description, etc..",
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         description="sort",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="newest", value="newest", summary="newest"),
     *         @OA\Examples(example="top", value="top", summary="top"),
     *     ),
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
     *         description="gender",
     *         in="query",
     *         name="gender",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="male", value="male", summary="male"),
     *         @OA\Examples(example="female", value="female", summary="female"),
     *     ),
     *     @OA\Parameter(
     *         description="ethnicity",
     *         in="query",
     *         name="ethnicity",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         @OA\Examples(example="caucasian", value="caucasian", summary="caucasian"),
     *         @OA\Examples(example="african american", value="african american", summary="african american"),
     *         @OA\Examples(example="japanese", value="japanese", summary="japanese"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/Result"),
     *             },
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/StockListCountResources"),
     *         ),
     *      ),
     *   ),
     * )
     *
     * @throws Throwable
     */
    public function index(IndexStockRequest $request)
    {
        $validated = $request->validated();
        return $this->run(function () use ($validated) {
            $search = $validated['search'] ?? null;
            $sort = Sorts::from($validated['sort'] ?? Sorts::TOP->value);
            $page = $validated['page'] ?? 1;
            $gender = $validated['gender'] ?? null;
            $ethnicity = $validated['ethnicity'] ?? null;
            $stock = $this->service->index($search, $sort, $page, $gender, $ethnicity, 33);
            $stock->setCollection($stock->transform(fn($item) => new StockListCountResources($item)));
            return SimplePaginator::transfer($stock);
        });
    }

    /**
     * Stock 리스트 페이지 필터 및 솔트 정보
     * @OA\Get(
     *     path="/api/stock/searchable-values",
     *     summary="stock searchable data",
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
     *               @OA\Property(
     *                 property="sorts",
     *                 type="array",
     *                 description="api sorts",
     *                 @OA\Items(ref="#/components/schemas/StockSortResources"),
     *                ),
     *              @OA\Property(
     *               property="filters",
     *               type="object",
     *               description="Filters for the API",
     *                    @OA\Property(
     *                        property="gender",
     *                        type="array",
     *                        description="Gender filters",
     *                        @OA\Items(ref="#/components/schemas/StockFilterGenderResources")
     *                    ),
     *                   @OA\Property(
     *                        property="ethnicity",
     *                        type="array",
     *                        description="Ethnicity filters",
     *                        @OA\Items(ref="#/components/schemas/StockFilterEthnicityResources")
     *                  ),
     *                ),
     *             ),
     *         ),
     *      ),
     * )
     *
     * @throws Throwable
     */
    public function searchableValues()
    {
        return $this->run(function () {
            return ResponseTemplate::toJson(
                [
                    'sorts'   => collect(Sorts::cases())->map(fn($item,
                    ) => new StockSortResources($item)),
                    'filters' => [
                        'gender'    => $this->service->getFilterByCode('gender')
                                                     ->map(fn($item,
                                                     ) => new StockFilterGenderResources($item))
                                                     ->prepend([
                                                         'code' => null,
                                                         'name' => 'All',
                                                     ],
                                                     ),
                        'ethnicity' => $this->service->getFilterByCode('ethnicity')
                                                     ->map(fn($item,
                                                     ) => new StockFilterEthnicityResources($item))
                                                     ->prepend([
                                                         'code' => null,
                                                         'name' => 'All',
                                                     ],
                                                     ),
                    ],
                ],
            );
        });
    }

    /**
     * stock image 생성을 위해 image generate api 호출 후 callback
     * @OA\Post(
     *     path="/api/stock/{stock}/generate",
     *     summary="stock generate",
     *     tags={"stock"},
     *    @OA\Parameter(
     *      in="path",
     *      name="stock",
     *      required=true,
     *      description="stock id",
     *      example="1"
     *     ),
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
     *         name="pose_variation",
     *         required=false,
     *         description="pose_variation",
     *         example="true",
     *         @OA\Schema(type="bool"),
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
    public function generate(\App\Models\Stock\Stock $stock, GenerateStockRequest $request)
    {
        $validated = $request->validated();
        $callback = function () use ($stock, $validated) {
            $response = $this->service->generate(
                stock          : $stock,
                user           : AuthService::currentUser(),
                ethnicity      : $validated['ethnicity'] ?? $stock->generateInformation->race,
                gender         : $validated['gender'] ?? $stock->generateInformation->gender,
                age            : $validated['age'] ?? null,
                isSkinReality  : $validated['skin_reality'] ?? false,
                isPoseVariation: $validated['pose_variation'] ?? false,
            );
            return ResponseTemplate::toJson(
                message: $response,
            );
        };
        return $this->transaction($callback);
    }
}
