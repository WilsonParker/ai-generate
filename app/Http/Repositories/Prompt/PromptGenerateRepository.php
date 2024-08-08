<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BasePaginationRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use AIGenerate\Models\User\User;

class PromptGenerateRepository extends BasePaginationRepository
{
    public function getGenerateList(array $attributes, callable $sortCallback): Paginator
    {
        return $this->setPaginateOption($attributes['user']->promptGenerates(), $attributes, $sortCallback);
    }

    /**
     * @param               $query
     * @param array         $attributes
     * @param callable|null $sortCallback
     * @return \Illuminate\Contracts\Pagination\Paginator
     * @author
     * @added   2023/05/11
     * @updated 2023/05/11
     */
    public function setPaginateOption($query, array $attributes, ?callable $sortCallback = null): Paginator
    {
        if (isset($attributes['sort'])) {
            $sortCallback($query, $attributes['sort']);
        } else {
            $query->orderBy('id', 'desc');
        }
        return
            $query->paginate(
                $attributes['size'] ?? 10,
                ['*'],
                'page',
                $attributes['page'] ?? 1,
            );
    }

    public function getCreateList(array $attributes, callable $sortCallback): Paginator
    {
        return $this->setPaginateOption($attributes['user']->prompts(), $attributes, $sortCallback);
    }

    public function getSellerPromptGeneratedPagination(
        User $user,
        ?Carbon $start,
        ?Carbon $end,
        array $attributes,
        ?callable $sortCallback = null,
    ): Paginator {
        return $this->setPaginateOption(
            $this->getSellerPromptGeneratedQuery($user, $start, $end),
            $attributes,
            $sortCallback,
        );
    }

    public function getSellerPromptGeneratedQuery(
        User $user,
        ?Carbon $start = null,
        ?Carbon $end = null,
    ) {
        return $user->sellerPromptGenerates()
                    ->when(isset($start), fn($query,
                    ) => $query->where('prompt_generates.created_at', '>=', $start->hour(0)->minute(0)->second(0)))
                    ->when(isset($end), fn($query,
                    ) => $query->where('prompt_generates.created_at', '<=', $end->hour(23)->minute(59)->second(59)));
    }

    public function getSellerPromptGeneratedGroupByPromptPagination(
        User $user,
        ?Carbon $start,
        ?Carbon $end,
        array $attributes,
        ?callable $sortCallback = null,
    ): Paginator {
        return $this->setPaginateOption(
            $this->getSellerPromptGeneratedGroupByPromptQuery($user, $start, $end),
            $attributes,
            $sortCallback,
        );
    }

    public function getSellerPromptGeneratedGroupByPromptQuery(
        User $user,
        ?Carbon $start,
        ?Carbon $end,
    ) {
        return $user->prompts()->whereHas('generates', function ($query) use ($start, $end) {
            $query->when(
                isset($start),
                fn($query) => $query->where(
                    'prompt_generates.created_at',
                    '>=',
                    $start->hour(0)->minute(0)->second(0),
                ),
            )->when(
                isset($end),
                fn($query) => $query->where(
                    'prompt_generates.created_at',
                    '<=',
                    $end->hour(23)->minute(59)->second(59),
                ),
            );
        });
    }
}
