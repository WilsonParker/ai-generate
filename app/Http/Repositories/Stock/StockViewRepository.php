<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\User\User;

class StockViewRepository extends BaseRepository
{
    public function add(Stock $stock, User $user): Model
    {
        $now = now()->format('Y-m-d');
        $stockView = $this->firstOrCreate([
            'stock_id' => $stock->getKey(),
            'user_id'  => $user->getKey(),
            'date'     => $now,
        ]);
        $stockView->increment('views');
        $stockView->save();
        $stock->save();
        return $stockView;
    }
}
