<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class StockRecommendRepository extends BaseRepository
{
    public function getRandomRecommend(int $limit = 11, array $filter = []): Collection
    {
        return $this->model::when(isset($filter['search']), function ($q) use ($filter) {
            $q->where(function ($query) use ($filter) {
                $searchTerm = '%' . $filter['search'] . '%';
                $query->whereHas('information', fn($query) => $query->where('gender', 'like', $searchTerm)
                    ->orWhere('ethnicity', 'like', $searchTerm));
                $query->orWhereHas('stock', fn($query) => $query->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm));
            });
        })
            ->when(isset($filter['gender']), function ($q) use ($filter) {
                $q->whereHas('information', fn($query) => $query->where('gender', $filter['gender']));
            })
            ->when(isset($filter['ethnicity']), function ($q) use ($filter) {
                $q->whereHas('information', fn($query) => $query->where('ethnicity', $filter['ethnicity']));
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
