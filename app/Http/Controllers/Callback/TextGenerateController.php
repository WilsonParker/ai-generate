<?php

namespace App\Http\Controllers\Callback;

use App\Events\Stock\TextGeneratedEvent;
use App\Http\Controllers\BaseController;
use App\Http\Repositories\Generate\TextGenerateRepository;
use App\Http\Response\Facades\ResponseTemplate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TextGenerateController extends BaseController
{
    public function __construct(private TextGenerateRepository $repository) {}

    /*
     * stock image 생성을 위해 image generate api 호출 후 callback
     * */
    public function generated(Request $request)
    {
        return $this->run(function () use ($request) {
            $data = $request->input('data');
            if (isset($data)) {
                $payload = json_decode($data['payload'], true);
                $generate = $this->repository->setSuccess($payload['id']);
                foreach ($data['result'] as $image) {
                    $generate->addMediaFromUrl($image)
                             ->withCustomProperties($payload)
                             ->toMediaCollection('gallery');
                }
                $this->eventComplete($generate, $payload);

                return ResponseTemplate::toJson(
                    status : ResponseAlias::HTTP_OK,
                    message: 'success',
                );
            } else {
                return ResponseTemplate::toJson(
                    status : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    message: $request->input('message', 'error'),
                );
            }
        });
    }

    protected function eventComplete($generate, array $payload): void
    {
        TextGeneratedEvent::dispatch($generate);
    }

}
