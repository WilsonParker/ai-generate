<?php

namespace App\Http\Repositories\Stock;

use App\Http\Repositories\BaseRepository;
use AIGenerate\Models\Stock\StockExport;
use AIGenerate\Models\Stock\StockGenerate;
use AIGenerate\Services\Stock\Contracts\StockExportRepositoryContract;

class StockExportRepository extends BaseRepository implements StockExportRepositoryContract
{
    public function store(StockGenerate $stockGenerate): StockExport
    {
        return $this->create(['stock_generate_id' => $stockGenerate->id]);
    }
}
