<?php

namespace App\Http\Controllers\Callback;

use App\Events\Stock\StockGeneratedEvent;
use App\Http\Controllers\BaseController;
use App\Http\Repositories\Stock\StockGenerateRepository;
use App\Http\Response\Facades\ResponseTemplate;
use App\Models\User\User;
use Illuminate\Http\Request;
use AIGenerate\Models\Stock\Enums\MediaType;
use AIGenerate\Models\Stock\Stock;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class StockController extends BaseController
{
    public function __construct(private StockGenerateRepository $repository) {}

    /*
     * stock image 생성을 위해 image generate api 호출 후 callback
     * */
    public function generated(Request $request)
    {
        $data = $request->input('data');
        if (isset($data)) {
            $payload = json_decode($data['payload'], true);
            $stock = Stock::findOrFail($payload['id']);

            if ($payload['type'] == MediaType::Detail->value) {
                // stock image 생성 일 경우
                foreach ($data['result'] as $image) {
                    $stock->addMediaFromUrl($image)
                          ->withCustomProperties($payload)
                          ->toMediaCollection('gallery');
                }
            } else if ($payload['type'] == MediaType::Generated->value) {
                // stock generate 일 경우
                $user = User::findOrFail($payload['user_id']);
                $stockGenerate = $this->repository->store(
                    $stock,
                    $user,
                    $payload['ethnicity'] ?? null,
                    $payload['gender'] ?? null,
                    isset($payload['age']) || $payload['age'] != "" ? (int)$payload['age'] : null,
                    $payload['lora'] ? true : false,
                    $payload['denoising_strength'] == 15,
                );
                foreach ($data['result'] as $image) {
                    $stockGenerate->addMediaFromUrl($image)
                                  ->withCustomProperties($payload)
                                  ->toMediaCollection('gallery');
                }
                StockGeneratedEvent::dispatch($stockGenerate);
            }

            return ResponseTemplate::toJson(
                status : ResponseAlias::HTTP_OK,
                message: 'success',
            );
        } else {
            return ResponseTemplate::toJson(
                status : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                message: $request->input('message'),
            );
        }
    }

    public function generatedThumbnail(Request $request)
    {
        $data = $request->input('data');
        if (isset($data)) {
            $payload = json_decode($data['payload'], true);
            $stock = Stock::findOrFail($payload['id']);

            if ($payload['type'] == MediaType::Detail->value) {
                // stock detail image 생성 일 경우
                foreach ($data['result'] as $image) {
                    $stock->addMediaFromUrl($image)
                          ->withCustomProperties($payload)
                          ->toMediaCollection('detail');
                }
            }
            return ResponseTemplate::toJson(
                status : ResponseAlias::HTTP_OK,
                message: 'success',
            );
        }

        return ResponseTemplate::toJson(
            status : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
            message: $request->input('message'),
        );
    }
}
