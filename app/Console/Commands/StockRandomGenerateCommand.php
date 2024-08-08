<?php

namespace App\Console\Commands;

use App\Models\Stock\Stock;
use App\Models\User\User;
use Illuminate\Console\Command;
use AIGenerate\Models\Stock\Enums\Ethnicity;
use AIGenerate\Models\Stock\Enums\Gender;
use AIGenerate\Services\Stock\StockService;

class StockRandomGenerateCommand extends Command
{
    protected $signature = 'stock:random-generate 
                            {id : stock id}
                            {email : user email}
                            {--count=1 : generate count}
    ';

    protected $description = 'Command description';

    public function __construct(private StockService $service)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        for ($i = 1; $i <= $this->option('count'); $i++) {
            $stock = Stock::findOrFail($this->argument('id'));
            $user = User::where('email', $this->argument('email'))->firstOrFail();

            $ethnicity = collect(Ethnicity::cases())->random(1)->first();
            $gender = collect(Gender::cases())->random(1)->first();
            $age = rand(5, 95);
            $isSkinReality = collect([true, false])->random(1)->first();
            $isPoseVariation = collect([true, false])->random(1)->first();
            $this->service->generate(
                stock          : $stock,
                user           : $user,
                ethnicity      : $ethnicity,
                gender         : $gender,
                age            : $age,
                isSkinReality  : $isSkinReality,
                isPoseVariation: $isPoseVariation,
            );
        }
    }
}
