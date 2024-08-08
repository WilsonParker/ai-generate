<?php

namespace App\Http\Repositories\Prompt;

use App\Http\Repositories\BaseRepository;
use App\Http\Repositories\Prompt\Contracts\PromptDetailRepositoryContract;
use App\Models\Prompt\Prompt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use AIGenerate\Models\Prompt\PromptCount;
use AIGenerate\Models\User\User;

class PromptInfinityScrollRepository extends BaseRepository implements PromptDetailRepositoryContract
{

    /*
     * infinity scroll function
     * size = 5 로 가정
     * last = 0 일 경우 size 만큼 가져온다.
     *
     * 1 page 에서 5 개의 list 가 [{0, 1, 2, 3, 4}] 일 때 모든 item 을 가져옵니다. (last = 4)
     * 2 page 에서 5 개의 list 가 [{5, 6, 7, 8, 9}] 일 때 last (4)에 해당하는 item 이 없기 때문에 모든 item 을 가져옵니다. (last = 9)
     * 4 page 에서 5 개의 list 가 [8, 9, {10, 11, 12}] 일 때 last (9)에 해당하는 item 이 있기 때문에 size(5) - 9의 index + 1 (2) = 3 개 item 을 가져옵니다. (last = 12)
     * 4 page 에서 3 개의 item 을 가지고 왔고 2개의 item 을 더 가져와야 하기 때문에 5 page 에서 2 개의 item 을 가져옵니다. (last = 14)
     *
     * example) 5 page 의 list 가 각각 아래와 같을 때 가져오는 값{} 예시 입니다.
     * [{13, 14}, 15, 16, 17]
     * [11, 12, {13, 14}, 15]
     * [9, 10, 11, 12, {13}] [{14}, 15, 16, 17, 18] => 5 page 에서 13, 6 page 에서 14를 가져옵니다.
     *
     * */
    public function index(array $attributes, callable $sortCallback): Collection
    {
        $collect = collect();
        $size = $attributes['size'];
        $last = $attributes['last'];
        // 첫 페이지 부터 last 값을 찾기 위해 0 으로 설정
        $attributes['page'] = 0;
        do {
            // 더 적은 query 실행을 위해 size 를 2배로 늘려서 last 를 찾습니다
            $attributes['size'] = $size * 2;
            $result = $this->paginate($attributes, $sortCallback);
            $items = collect($result->items());

            if ($result->count() == 0) {
                break;
            }

            // last 값이 있을 경우
            if ($attributes['last'] != 0) {
                $keys = $this->getItemKeys($items);

                // last 값이 현재 결과에 존재할 경우
                if ($this->hasItem($keys, $last)) {
                    // last 이후로 size 만큼 slice 합니다.
                    $lastTemp = $this->sliceLast($last, $items, $size, $collect);

                    // $collect 에 size 만큼 item 이 채워졌을 경우 break 합니다.
                    if ($collect->count() == $size) {
                        break;
                    }
                    // 그렇지 않을 경우 다음 페이지의 값을 가져옵니다.
                    $attributes['page'] = $result->currentPage() + 1;
                    $result = $this->paginate($attributes, $sortCallback);
                    $last = $this->sliceLast($lastTemp, $result, $size, $collect);
                }
            } else {
                // last 값이 없을 경우 size 만큼 slice 합니다.
                $slice = $items->splice(0, $size - $collect->count());
                $collect = $collect->merge($slice);
            }
            $attributes['page'] = $result->currentPage() + 1;

        } while ($collect->count() < $size && $result->hasMorePages());
        return $collect;
    }

    protected function paginate(array $attributes, callable $sortCallback): LengthAwarePaginator
    {
        return Prompt::search($attributes['search'] ?? null)
                     ->when($attributes['types'] ?? null, function ($query) use ($attributes) {
                         $query->whereIn('type', $attributes['types']);
                     })
                     ->when($attributes['categories'] ?? null, function ($query) use ($attributes) {
                         $query->whereIn('categories', $attributes['categories']);
                     })
                     ->when($attributes['sort'] ?? null, function ($query) use ($attributes, $sortCallback) {
                         $sortCallback($query);
                     })
                     ->paginate($attributes['size'], 'page', $attributes['page'] ?? 1);
    }

    private function getItemKeys(Collection|Paginator $items): Collection
    {
        if ($items instanceof Paginator) {
            $items = collect($items->items());
        }
        return $items->pluck('id');
    }

    protected function hasItem(Collection $keys, int $last): bool
    {
        return $keys->first(fn($item) => $item == $last) != null;
    }

    private function sliceLast(int $last, Collection|Paginator $items, int $size, Collection &$collect): int
    {
        $keys = $this->getItemKeys($items);
        $start = $this->hasItem($keys, $last) ? $this->lastIndex($this->getItemKeys($items), $last) + 1 : 0;

        if ($items instanceof Paginator) {
            $items = collect($items->items());
        }
        $slice = $items->splice($start, $size - $collect->count());
        $collect = $collect->merge($slice);
        return $collect->isEmpty() ? $start : $this->getLastKey($collect);
    }

    protected function lastIndex(Collection $keys, int $last): int
    {
        return $keys->search(fn($item) => $item == $last);
    }

    private function getLastKey(Collection $collection): int
    {
        return $collection->last()->getKey();
    }

    public function generatedPrompts(User $user, Prompt $prompt): Collection
    {
        return $user->promptGenerates()->where('prompt_id', $prompt->getKey())->get();
    }

    public function otherPrompts(Prompt $prompt): Collection
    {
        return $prompt->otherPrompts()
                      ->orderByDesc(
                          PromptCount::select('generated')->whereColumn('prompt_count.prompt_id', 'prompts.id'),
                      )->get();
    }

    public function main(): Collection
    {
        return $this->model::enabled()->inRandomOrder()->limit(10)->get();
    }

    public function newPrompts(): Collection
    {
        return $this->model::enabled()->orderBy('created_at', 'desc')->limit(10)->get();
    }

    public function popularPrompts(int $limit = 10): Collection
    {
        return $this->model::enabled()->inRandomOrder()->limit($limit)->get();
    }

    public function isFavorite(User $user, Prompt $prompt): bool
    {
        return $user->favorites()->where('prompt_id', $prompt->getKey())->exists();
    }
}
