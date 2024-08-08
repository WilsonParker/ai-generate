<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="id",
 *     type="integer",
 *     description="id",
 *         @OA\Examples(value="1"),
 *  ),
 *  @OA\Property(
 *     property="result",
 *     type="string",
 *     description="api result",
 *     example="She did not go to the market."
 *  )
 * )
 * Class PromptCompletionGenerateResultResources
 *
 * @package App\Resources\Prompt
 */
class PromptCompletionGenerateResultResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'result' => collect(json_decode($this->result)->choices)->implode('text', '\n'),
        ];
    }

}
