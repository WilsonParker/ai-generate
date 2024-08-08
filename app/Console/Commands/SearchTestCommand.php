<?php

namespace App\Console\Commands;

use App\Models\Stock\Stock;
use Illuminate\Console\Command;

class SearchTestCommand extends Command
{
    protected $signature = 'search:test {keyword}';

    protected $description = 'Command description';

    public function handle(): void
    {
        $keyword = $this->argument('keyword');
        dump($keyword);
        $stocks = Stock::search($keyword)->paginate(10);
        dump($stocks);
    }
}
