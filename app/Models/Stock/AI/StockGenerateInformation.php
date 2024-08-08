<?php

namespace App\Models\Stock\AI;

use AIGenerate\Models\BaseModel;

class StockGenerateInformation extends BaseModel
{
    protected $connection = 'ai';
    protected $table = 'stock_generate_information';

}
