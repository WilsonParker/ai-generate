<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *      @OA\Property(property="id", type="integer", description="primary key", readOnly="true", example="1"),
 * )
 * Class BaseIdResources
 *
 * @package App\Resources
 */
abstract class BaseIdResources extends BaseResources
{
    protected bool $showAppendFields = true;

    function appendsFields(Request $request): array
    {
        return $this->showAppendFields ? ['id' => $this->id] : [];
    }

}
