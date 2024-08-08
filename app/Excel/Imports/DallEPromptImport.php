<?php

namespace App\Excel\Imports;

use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Prompt\PromptService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use AIGenerate\Models\Prompt\Enums\Status;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptCategory;

class DallEPromptImport implements ToCollection
{
    public function __construct(
        private readonly PromptService $promptService,
        private readonly ImageServiceContract $imageService,
    ) {}

    public function collection(Collection $collection)
    {
        $disk = config('media-library.disk_name');
        foreach ($collection as $idx => $row) {
            if ($idx < $this->getHeaderIndex()) {
                continue;
            }
            if (!$this->hasRow($row)) {
                break;
            }
            dump($idx);

            $prompt = Prompt::create([
                'user_id'            => $this->getRandomUserId(),
                'prompt_status_code' => Status::Enabled->value,
                'prompt_type_code'   => 'image',
                'prompt_engine_code' => 'default',
                'title'              => "{$this->getTitle($row)}",
                'description'        => "{$this->getDescription($row)}",
                'template'           => "{$this->getTemplate($row)}",
                'guide'              => "{$this->getUserGuide($row)}",
                'price_per_generate' => $this->getPrice($row),
            ]);

            $prompt->count()->create();
            $this->promptService->storeOptions($prompt, $prompt->template);
            $this->getTags($row)->each(fn($tag) => $prompt->tags()->firstOrCreate(['name' => $tag]));
            $category = PromptCategory::where(['name' => $this->getCategory($row)])->first();
            $prompt->categories()->attach($category);

            $id = $this->getId($row);
            $dir = "/PromptAsset/$id";
            $images = collect(Storage::disk($disk)->files($dir))
                ->map(function ($image) use ($prompt, $disk) {
                    return $prompt
                        // ->addMediaFromUrl(config('filesystems.disks.media.url') . '/' . $image)
                        ->addMediaFromDisk($image, $disk)
                        ->usingFileName(md5(Str::of($image)->afterLast('/')))
                        ->toMediaCollection('gallery', $disk);
                });
            $prompt->images()->saveMany($images);
            $prompt->save();
        }
    }

    private function getHeaderIndex(): int
    {
        return 1;
    }

    private function hasRow(Collection $row): bool
    {
        return !empty($row[2]);
    }

    private function getRandomUserId(): int
    {
        return rand(1, 10);
    }

    private function getTitle(Collection $row): string
    {
        return $row[5];
    }

    private function getDescription(Collection $row): string
    {
        return $row[6];
    }

    private function getTemplate(Collection $row): string
    {
        return $row[8];
    }

    private function getUserGuide(Collection $row): string
    {
        return $row[7];
    }

    private function getPrice(Collection $row): string
    {
        return $row[10];
    }

    private function getTags(Collection $row): Collection
    {
        return collect(explode('#', $row[9]))->map(fn($tag) => trim($tag))->filter();
    }

    private function getCategory(Collection $row): string
    {
        return $row[4];
    }

    private function getId(Collection $row): int
    {
        return $row[1];
    }

    private function getOutputExample(Collection $row): string
    {
        return '';
    }
}
