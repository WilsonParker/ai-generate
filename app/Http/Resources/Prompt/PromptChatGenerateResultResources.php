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
 *     example="The 2020 World Series was played at Globe Life Field in Arlington, Texas."
 *  )
 * )
 * Class PromptChatGenerateResultResources
 *
 * @package App\Resources\Prompt
 */
class PromptChatGenerateResultResources extends BaseIdResources
{
    public function fields($request): array
    {
        return [
            'result' => json_decode($this->result)->choices[0]->message->content,
        ];
    }

}
