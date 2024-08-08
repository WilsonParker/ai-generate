<?php

namespace App\Http\Response;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ResponseTemplate extends JsonResponse implements Arrayable
{
    public function toJson(
        $data = null,
        string $message = '',
        int $status = ResponseAlias::HTTP_OK,
        array $headers = [],
        int $options = 0
    ): JsonResponse {
        $this->message = $message;
        $this->data = $data;
        return response()->json($this->toArray(), $status, $headers, $options);
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

}
