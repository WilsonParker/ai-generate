<?php

namespace App\Services\Prompt;

use App\Http\Repositories\Prompt\PromptGenerateRepository;
use App\Services\Prompt\Sorts\Sorts;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use AIGenerate\Models\User\User;

class PromptGenerateService
{
    public function __construct(
        protected readonly PromptGenerateRepository $repository,
        protected readonly Sorts $sorts,
    ) {}

    public function getGenerateList(
        User $user,
        int $page = 0,
        int $size = 10,
    ): Paginator {
        return $this->repository->getGenerateList([
            'user' => $user,
            'page' => $page,
            'size' => $size,
        ], function ($query) {});
    }

    public function getCreateList(
        User $user,
        int $page = 0,
        int $size = 10,
    ): Paginator {
        return $this->repository->getCreateList([
            'user' => $user,
            'page' => $page,
            'size' => $size,
        ], function ($query) {});
    }

    /**
     * start, end 기간 동안 판매 금액을 제공합니다
     *
     * @param \AIGenerate\Models\User\User $user
     * @param \Carbon\Carbon               $start
     * @param \Carbon\Carbon               $end
     * @return float
     * @author  allen
     * @added   2023/05/09
     * @updated 2023/05/09
     */
    public function getSalesPrice(User $user, Carbon $start, Carbon $end): float
    {
        $generated = $this->repository->getSellerPromptGeneratedQuery($user, $start, $end,);
        return $this->calculateSalesPrice($generated->get()->sum('seller_price'));
    }

    public function calculateSalesPrice(float|int $price): float
    {
        $calculated = $price * 0.8;
        return floor($calculated * 100) / 100;
    }

    public function getTotalSalesPrice(User $user): float
    {
        $generated = $this->repository->getSellerPromptGeneratedQuery($user);
        return $this->calculateSalesPrice($generated->get()->sum('seller_price'));
    }

    public function getSellerPromptGenerated(
        User $user,
        ?Carbon $start,
        ?Carbon $end,
        array $attributes,
    ): Paginator {
        return $this->repository->getSellerPromptGeneratedPagination(
            $user,
            $start,
            $end,
            $attributes,
            function ($query) {},
        );
    }

    public function getSellerPromptGeneratedGroupByPrompt(
        User $user,
        ?Carbon $start,
        ?Carbon $end,
        array $attributes,
    ): Paginator {
        return $this->repository->getSellerPromptGeneratedGroupByPromptPagination(
            $user,
            $start,
            $end,
            $attributes,
            function ($query) {},
        );
    }
}
