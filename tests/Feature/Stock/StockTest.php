<?php

namespace Tests\Feature\Stock;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Events\Stock\StockGeneratedEvent;
use App\Models\User\User;
use App\Services\Auth\Facades\AuthService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use AIGenerate\Models\Stock\Enums\Aged;
use AIGenerate\Models\Stock\Enums\Friendly;
use AIGenerate\Models\Stock\Stock;
use AIGenerate\Models\Stock\StockGenerate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class StockTest extends TestCase
{
    public function test_stock_generate_is_successfule()
    {
        $stock = Stock::first();
        $user = AuthService::testUser();
        $this->actingAs($user, 'api');
        $response = $this->post(route('api.stock.generate', $stock->getKey()), [
            'aged'     => 100,
            'friendly' => 50,
        ]);
        $response->assertStatus(200);
    }

    public function test_stock_generate_event()
    {
        $stockGenerate = StockGenerate::first();
        StockGeneratedEvent::dispatch($stockGenerate);
    }

    public function test_stock_image()
    {
        $user = User::find(1);
        /**
         * @var Media $image
         */
        $image = $user->stockGenerates->first()->images->first();
        dump($image->getCustomProperty('aged'));
        dump($image->getCustomProperty('friendly'));
        dump($image->getUrl('gallery-thumbnail'));
    }

    public function test_stock_has_image_count()
    {
        $count = Stock::where(function ($whereQuery) {
            $whereQuery->whereHas('images', function ($subQuery) {
                $subQuery->where('custom_properties->aged', Aged::High->getValue())
                         ->where('custom_properties->friendly', Friendly::High->getValue());
            })->whereHas('images', function ($subQuery) {
                $subQuery->where('custom_properties->aged', Aged::High->getValue())
                         ->where('custom_properties->friendly', Friendly::Low->getValue());
            })->whereHas('images', function ($subQuery) {
                $subQuery->where('custom_properties->aged', Aged::Low->getValue())
                         ->where('custom_properties->friendly', Friendly::High->getValue());
            })->whereHas('images', function ($subQuery) {
                $subQuery->where('custom_properties->aged', Aged::Low->getValue())
                         ->where('custom_properties->friendly', Friendly::Low->getValue());
            });
        })->count();
        echo $count;
        $this->assertIsNumeric($count);
    }

    public function test_stock_image_url_is_correct()
    {
        $stock = \App\Models\Stock\Stock::with('images')->whereHas('images')->first();
        $image = $stock->images->first();
        $link = Storage::disk('media')
                       ->temporaryUrl(config('filesystems.path') . '/' . $image->getPath(), now()->addMinutes(5));
        $link2 = $image->getOriginalTemporaryUrl();
        $this->assertEquals($link, $link2);
    }

    public function test_stock_search()
    {
        // 22256
        $paginate = Stock::search('girl')
                         ->options([
                             'offset' => 10000,
                             'page'   => 101,
                         ])->paginate(10, 'page', 101);
        dump($paginate);
    }

    public function test_stcok_origin_image_is_corrcet()
    {
        $stock = \App\Models\Stock\Stock::with('origin.images')->first();
        $url = $stock->origin->images->first()->getUrl('gallery-crop');
        $this->assertStringContainsString('ai_generate-ai/stock-crop/1872/conversions/4HBvj1fBvgzQXp0VPvkRq4YDaeBtRHwL-gallery-crop.jpg', $url);
    }

    public function test_stock_cache()
    {
        $stocks = \App\Models\Stock\AI\Stock::all();
        dd($stocks->count());
        Cache::remember('stocks', 60 * 60 * 24, function () use ($stocks) {
            return $stocks;
        });
    }

}
