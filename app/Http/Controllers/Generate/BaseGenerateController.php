<?php

namespace App\Http\Controllers\Generate;

use App\Http\Controllers\BaseController;
use App\Http\Response\Facades\Paginator;
use App\Http\Response\Facades\ResponseTemplate;
use App\Services\Auth\Facades\AuthService;
use Illuminate\Database\Eloquent\Model;
use AIGenerate\Services\Generate\Enums\SamplingMethod;
use AIGenerate\Services\Generate\Extensions\Adetailer\Enums\Models;

abstract class BaseGenerateController extends BaseController
{
    protected string $exportResourceClass;
    protected string $indexResourceClass;

    protected function baseForms()
    {
        return $this->run(function () {
            return ResponseTemplate::toJson(
                [
                    'sampling_method' => SamplingMethod::getEnumAttributes(keyCallback: 'value'),
                    'lora'            => [
                        'vodka_portraits',
                    ],
                    'extension'       => [
                        'adetailer' => [
                            'ad_model' => Models::getEnumAttributes(),
                        ],
                    ],
                ],
            );
        });
    }

    protected function baseExportImageUrl(Model $generate)
    {
        return $this->run(function () use ($generate) {
            $this->authorize('exportImageUrl', $generate);
            $export = $this->getService()->storeExport($generate);
            $export->imageUrl = $generate->images->last()?->getOriginalTemporaryUrl();
            return ResponseTemplate::toJson(new $this->exportResourceClass($export));
        });
    }

    abstract protected function getService();

    protected function baseDestroy(Model $generate)
    {
        $this->authorize('destroy', $generate);
        return $this->run(function () use ($generate) {
            return ResponseTemplate::toJson(
                message: $this->getService()->destroy($generate),
            );
        });
    }

    protected function baseIndex(array $validated)
    {
        return $this->run(function () use ($validated) {
            $generate = $this->getService()->index(AuthService::currentUser(), $validated['page'] ?? 1, $validated['size'] ?? 6);
            $generate->setCollection($generate->transform(fn($item) => new $this->indexResourceClass($item)));
            return Paginator::transfer($generate);
        });
    }
}
