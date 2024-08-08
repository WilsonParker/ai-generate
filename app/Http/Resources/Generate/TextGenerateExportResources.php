<?php

namespace App\Http\Resources\Generate;

use App\Http\Resources\BaseIdResources;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *  @OA\Property(
 *     property="image_url",
 *     type="string",
 *     description="image url",
 *     example="https://example.com/image.png"
 *  ),
 *  @OA\Property(
 *     property="user_id",
 *     type="integer",
 *     description="user id",
 *     example="1"
 *  ),
 *  @OA\Property(
 *     property="generate_id",
 *     type="integer",
 *     description="generate_id",
 *     example="1"
 *  ),
 * @OA\Property(
 *     property="exports",
 *     type="integer",
 *     description="exports",
 *     example="1"
 *  ),
 * )
 * Class TextGenerateExportResources
 *
 * @package App\Resources\Stock\TextGenerateExportResources
 */
class TextGenerateExportResources extends BaseIdResources
{
    public function fields(Request $request): array
    {
        return [
            'image_url' => $this->imageUrl,
            'user_id' => $this->generate->user_id,
            'generate_id' => $this->generate->id,
            'exports' => $this->generate->user->count->text_generate_exports,
            // 'exports' => $this->generate->user->count->exports,
        ];
    }
}
