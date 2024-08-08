<?php

namespace App\Services\Prompt;

use App\Http\Repositories\Prompt\Contracts\PromptDetailRepositoryContract;
use App\Http\Repositories\Prompt\PromptCategoryRepository;
use App\Http\Repositories\Prompt\PromptEngineRepository;
use App\Http\Repositories\Prompt\PromptGenerateOutputOptionRepository;
use App\Http\Repositories\Prompt\PromptTypeRepository;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Prompt\Contracts\PromptServiceContract;
use App\Services\Prompt\Sorts\Enums\Sorts;
use App\Services\Prompt\ThumbnailComposite\ThumbnailComposite;
use App\Services\Tag\TagService;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use AIGenerate\Models\Prompt\Enums\Status;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Models\Prompt\PromptOption;
use AIGenerate\Models\User\User;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;

class PromptService implements PromptServiceContract, \AIGenerate\Services\Mails\Contracts\PromptServiceContract
{
    public function __construct(
        protected readonly PromptDetailRepositoryContract $repository,
        protected readonly PromptCategoryRepository $categoryRepository,
        protected readonly PromptTypeRepository $typeRepository,
        protected readonly PromptEngineRepository $engineRepository,
        protected readonly PromptGenerateOutputOptionRepository $outputOptionRepository,
        protected readonly TagService $tagService,
        protected readonly ImageServiceContract $imageService,
        protected readonly \App\Services\Prompt\Sorts\Sorts $sorts,
        protected readonly ThumbnailComposite $thumbnailComposite,
    ) {}

    public function update(Prompt $prompt, array $attributes): Model
    {
        if ($prompt->prompt_type_code == OpenAITypes::Completion->value) {
            $json = json_decode($attributes['template']);
            $template = $attributes['template'];
            $attributes['prompt_engine_code'] = $json->model;
        } else {
            $template = $attributes['template'];
            $attributes['prompt_engine_code'] = $attributes['engine'] ?? 'default';
        }
        $this->storeOptions($prompt, $template);

        $attributes['prompt_status_code'] = Status::Waiting->value;
        return $this->repository->update(
            $prompt,
            Arr::only($attributes, [
                'template',
                'guide',
                'order',
                'prompt_status_code',
                'prompt_engine_code',
            ]),
        );
    }

    public function storeOptions(
        Prompt $prompt,
        string $template,
    ): Prompt {
        $matches = $this->matchOptions($template);
        $options = collect($matches);
        $prompt->options()->delete();
        $prompt->options()->saveMany($options->map(fn($option) => new PromptOption(['name' => $option])));
        $prompt->load('options');
        return $prompt;
    }

    public function matchOptions(string $template): array
    {
        preg_match_all('/\[(.*?)\]/', $template, $matches);
        return collect($matches[1])->map(fn($item) => strtoupper($item))->unique()->toArray();
    }

    public function delete(Prompt $prompt): bool
    {
        return $prompt->delete();
    }

    public function store(array $attributes): Prompt
    {
        $prompt = $this->repository->create(
            array_merge(
                Arr::only($attributes, [
                    'user_id',
                    'title',
                    'description',
                    'output_result',
                ]),
                [
                    'prompt_type_code'   => $attributes['type'],
                    'price_per_generate' => $attributes['price'],
                    'prompt_status_code' => Status::Creating->value,
                ],
            ),
        );
        if (isset($attributes['images'])) {
            $images = collect($attributes['images'])->map(
                fn($image) => $this->imageService->upload($prompt, $image, 'gallery'),
            );
            $prompt->images()->saveMany($images);
        }
        $prompt->categories()->sync($attributes['category']);
        if (isset($attributes['tags'])) {
            $prompt->tags()->saveMany($this->tagService->create($attributes['tags']));
        }
        $prompt->save();
        return $prompt;
    }

    public function getCategories(): Collection
    {
        return $this->categoryRepository->all();
    }

    public function getTypes(): Collection
    {
        return $this->typeRepository->all();
    }

    public function main(): Collection
    {
        return $this->repository->main();
    }

    public function index(
        string $search = null,
        Sorts $sorts = Sorts::Newest,
        int $page = 0,
        int $size = 10,
        int $last = 0,
        array $types = [],
        array $categories = [],
    ): Paginator|Collection {
        return $this->repository->index([
            'search'   => $search,
            'sort'     => $sorts,
            'page'     => $page,
            'size'     => $size,
            'last'     => $last,
            'type'     => $types,
            'category' => $categories,
        ], function ($query) use ($sorts) {
            $this->sorts->sort($query, $sorts);
        });
    }

    public function storeWithOptions(
        User $user,
        string $title,
        string $description,
        string $template,
        string $promptTypeCode,
    ): Prompt {
        $prompt = $this->repository->create([
            'title'            => $title,
            'description'      => $description,
            'template'         => $template,
            'prompt_type_code' => $promptTypeCode,
            'user_id'          => $user->getKey(),
        ]);
        $this->storeOptions($prompt, $template);
        return $prompt;
    }

    public function getEngines(
        Prompt $prompt,
    ): Collection {
        return $this->engineRepository->getEngineByType(OpenAITypes::from($prompt->prompt_type_code));
    }

    public function generatedPrompts(User $user, Prompt $prompt): Collection
    {
        return $this->repository->generatedPrompts($user, $prompt);
    }

    public function getLanguages(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->outputOptionRepository->getLanguages();
    }

    public function getTones(): Collection
    {
        return $this->outputOptionRepository->getTones();
    }

    public function getWritingStyles(): Collection
    {
        return $this->outputOptionRepository->getWritingStyles();
    }

    public function otherPrompts(Prompt $prompt): Collection
    {
        return $this->repository->otherPrompts($prompt);
    }

    public function newPrompts(): Collection
    {
        return $this->repository->newPrompts();
    }

    public function getBestPrompts(): Collection
    {
        return $this->repository->popularPrompts(4);
    }

    public function popularPrompts(): Collection
    {
        return $this->repository->popularPrompts();
    }

    public function isFavorite(User $user, Prompt $prompt): bool
    {
        return $this->repository->isFavorite($user, $prompt);
    }
}
