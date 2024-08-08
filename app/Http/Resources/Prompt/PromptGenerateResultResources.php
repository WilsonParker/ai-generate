<?php

namespace App\Http\Resources\Prompt;

use App\Http\Resources\BaseIdResources;
use App\Services\Auth\Facades\AuthService;
use OpenApi\Annotations as OA;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="items",
 *     type="array",
 *     description="result array",
 *         @OA\Items(ref="#/components/schemas/PromptChatGenerateResultResources"),
 *  ),
 *  @OA\Property(
 *     property="remaining_point",
 *     type="float",
 *     description="remaining_point",
 *     example="100.00"
 *  ),
 *  @OA\Property(
 *     property="expired_at",
 *     type="string",
 *     description="expired_at",
 *     example="2021-08-31 00:00:00"
 *  ),
 * )
 * Class PromptGenerateResultResources
 */
class PromptGenerateResultResources extends BaseIdResources
{

    public function fields($request): array
    {
        $type = OpenAITypes::from($this->prompt->prompt_type_code);
        $user = AuthService::currentUser();
        return [
            'remaining_point' => round($user->getPoint(), 2, PHP_ROUND_HALF_DOWN),
            'items'           => $this->results->map(fn($item) => $type->newResources($item)),
            'expired_at'      => $this->formatDateTime($this->expired_at),
        ];
    }

}
