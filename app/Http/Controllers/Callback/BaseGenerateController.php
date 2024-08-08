<?php

namespace App\Http\Controllers\Callback;

use App\Http\Controllers\BaseController;
use App\Http\Response\Facades\ResponseTemplate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

abstract class BaseGenerateController extends BaseController
{
    /*
     * stock image 생성을 위해 image generate api 호출 후 callback
     * */
    public function generated(Request $request)
    {
        return $this->run(function () use ($request) {
            $data = $request->input('data');
            if (isset($data)) {
                $payload = json_decode($data['payload'], true);
                $generate = $this->getRepository()->setSuccess($payload['id']);
                foreach ($data['result'] as $image) {
                    $generate->addMediaFromUrl($image)
                             ->withCustomProperties($payload)
                             ->toMediaCollection('gallery');
                }
                $this->eventComplete($generate);

                return ResponseTemplate::toJson(
                    status : ResponseAlias::HTTP_OK,
                    message: 'success',
                );
            }

            return ResponseTemplate::toJson(
                status : ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                message: $request->input('message', 'error'),
            );
        });
    }

    abstract protected function getRepository();

    protected function eventComplete($generate): void
    {
        $this->getEventClass()::dispatch($generate);
    }

    abstract protected function getEventClass();
}
