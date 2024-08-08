<?php

namespace App\Excel\Imports;

use App\Models\Prompt\Prompt;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Prompt\PromptService;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use AIGenerate\Models\Prompt\Enums\Status;
use AIGenerate\Models\Prompt\PromptCategory;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

class TextPromptImport implements ToCollection
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

            $type = $this->getType($row);
            $engine = $this->getEngine($row);
            $template = $this->getTemplate($row);
            if ($type == OpenAITypes::Completion) {
                $decode = json_decode($template);
                $engine = $decode->model ?? $decode->engine;
            }

            $prompt = Prompt::create([
                'user_id'            => $this->getRandomUserId(),
                'prompt_status_code' => Status::Enabled->value,
                'prompt_type_code'   => $type->value,
                'prompt_engine_code' => $engine,
                'title'              => $this->getTitle($row),
                'description'        => $this->getDescription($row),
                'output_result'      => $this->getOutputExample($row),
                'template'           => $template,
                'guide'              => $this->getUserGuide($row),
                'price_per_generate' => $this->getPrice($row),
            ]);

            $prompt->count()->create();
            $this->promptService->storeOptions($prompt, $prompt->template);
            $this->getTags($row)->each(fn($tag) => $prompt->tags()->firstOrCreate(['name' => $tag]));
            $category = PromptCategory::where(['name' => $this->getCategory($row)])->first();
            $prompt->categories()->attach($category);

            $image = $this->getThumbnail($row);

            $file_from_path = storage_path('app/gpt/' . $image);
            $file_to_path = storage_path('app/gpt/clone-' . $image);

            // Clone the file
            \Illuminate\Support\Facades\File::copy($file_from_path, $file_to_path);
            $file = new File($file_to_path);
            $uploaded_file = new UploadedFile(
                $file->getPathname(),
                $file->getFilename(),
                $file->getMimeType(),
                $file->getSize(),
                false, // Set test mode to true to prevent moving the file
            );
            $images = $prompt
                // ->addMediaFromDisk($image, $disk)
                ->addMedia($uploaded_file)
                ->usingFileName(md5(Str::of($image)->afterLast('/')))
                ->toMediaCollection('gallery', $disk);
            $prompt->images()->saveMany(collect([$images]));
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

    private function getType(Collection $row): OpenAITypes
    {
        return match ($row[2]) {
            'GPTchat' => OpenAITypes::Chat,
            'GPTcompletion' => OpenAITypes::Completion,
        };
    }

    private function getEngine(Collection $row): string
    {
        // return $row[3];
        return $this->getType($row) == OpenAITypes::Chat ? 'gpt-3.5-turbo' : 'text-davinci-003';
    }

    private function getTemplate(Collection $row): string
    {
        return $row[9];
    }

    private function getRandomUserId(): int
    {
        return rand(1, 13);
    }

    private function getTitle(Collection $row): string
    {
        return $row[6];
    }

    private function getDescription(Collection $row): string
    {
        return $row[7];
    }

    private function getOutputExample(Collection $row): string
    {
        return $row[10] ?? '';
    }

    private function getUserGuide(Collection $row): string
    {
        return $row[8];
    }

    private function getPrice(Collection $row): string
    {
        $price = $row[12];
        return $price == 'FREE' ? 0 : $price;
    }

    private function getTags(Collection $row): Collection
    {
        return collect(explode('#', $row[11]))->map(fn($tag) => trim($tag))->filter();
    }

    private function getCategory(Collection $row): string
    {
        return $row[4];
    }

    private function getThumbnail(Collection $row): string
    {
        $val = (int)$row[5];
        $extension = match ($val) {
            1, 2, 4, 5, 6, 8 => 'jpg',
            3, 7, 9 => 'png',
        };
        return 'gpt_' . $val . '.' . $extension;
    }

    private function getId(Collection $row): string
    {
        return $row[1];
    }
}
