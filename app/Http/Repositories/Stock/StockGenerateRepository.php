<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use AIGenerate\Models\Stock\Stock;

class StockGenerateRepository extends BaseRepository
{

    public function store(
        Stock $stock,
        User $user,
        string $ethnicity,
        string $gender,
        ?int $age,
        bool $isSkinReality,
        bool $isPoseVariation,
    ): Model {
        return $this->model::create(
            [
                'stock_id'          => $stock->getKey(),
                'user_id'           => $user->getKey(),
                'ethnicity'         => $ethnicity,
                'gender'            => $gender,
                'age'               => $age,
                'is_skin_reality'   => $isSkinReality,
                'is_pose_variation' => $isPoseVariation,
            ]);
    }
}
