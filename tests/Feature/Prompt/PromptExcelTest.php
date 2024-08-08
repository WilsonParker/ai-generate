<?php

namespace Tests\Feature\Prompt;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Excel\Imports\DallEPromptImport;
use App\Excel\Imports\TextPromptImport;
use App\Excel\Imports\UserImport;
use App\Http\Repositories\Prompt\Contracts\PromptRepositoryContract;
use App\Http\Repositories\Prompt\PromptCategoryRepository;
use App\Http\Repositories\Prompt\PromptEngineRepository;
use App\Http\Repositories\Prompt\PromptGenerateOutputOptionRepository;
use App\Http\Repositories\Prompt\PromptTypeRepository;
use App\Services\Image\Contracts\ImageServiceContract;
use App\Services\Prompt\PromptService;
use App\Services\Prompt\Sorts\HottestSort;
use App\Services\Prompt\Sorts\NewestSort;
use App\Services\Prompt\Sorts\OldestSort;
use App\Services\Prompt\Sorts\RelevanceSort;
use App\Services\Prompt\Sorts\Sorts;
use App\Services\Prompt\Sorts\TopSort;
use App\Services\Prompt\ThumbnailComposite\ThumbnailComposite;
use App\Services\Tag\TagService;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PromptExcelTest extends TestCase
{
    public function test_read_dalle_prompt_excel(): void
    {
        // Upload the Excel file to the test storage disk
        $promptService = new PromptService(
            app()->make(PromptRepositoryContract::class),
            app()->make(PromptCategoryRepository::class),
            app()->make(PromptTypeRepository::class),
            app()->make(PromptEngineRepository::class),
            app()->make(PromptGenerateOutputOptionRepository::class),
            app()->make(TagService::class),
            app()->make(ImageServiceContract::class),
            new Sorts([
                app()->make(RelevanceSort::class),
                app()->make(HottestSort::class),
                app()->make(TopSort::class),
                app()->make(NewestSort::class),
                app()->make(OldestSort::class),
            ]),
            app()->make(ThumbnailComposite::class),
        );

        $imageService = app()->make(ImageServiceContract::class);
        Excel::import(new DallEPromptImport($promptService, $imageService), 'files/dalle.xlsx', 'local');
    }

    public function test_read_gpt_prompt_excel(): void
    {
        $promptService = new PromptService(
            app()->make(PromptRepositoryContract::class),
            app()->make(PromptCategoryRepository::class),
            app()->make(PromptTypeRepository::class),
            app()->make(PromptEngineRepository::class),
            app()->make(PromptGenerateOutputOptionRepository::class),
            app()->make(TagService::class),
            app()->make(ImageServiceContract::class),
            new Sorts([
                app()->make(RelevanceSort::class),
                app()->make(HottestSort::class),
                app()->make(TopSort::class),
                app()->make(NewestSort::class),
                app()->make(OldestSort::class),
            ]),
            app()->make(ThumbnailComposite::class),
        );

        $imageService = app()->make(ImageServiceContract::class);
        Excel::import(new TextPromptImport($promptService, $imageService), 'files/gpt.xlsx', 'local');
    }

    public function test_read_user_excel(): void
    {
        Excel::import(new UserImport(), 'files/user.xlsx', 'local');
    }

    /*public function test_prompt_set_thumbnail_first_image()
    {
        Prompt::with('images')
              ->get()
              ->each(function ($prompt) {
                  $prompt->images->each(function ($image, $idx) use ($prompt) {
                      if ($idx == 0) {
                          $prompt->thumbnail_id = $image->id;
                          $prompt->save();
                          // $this->assertEquals($prompt->thumbnail_id, $image->id);
                      }
                  });
              });
    }*/

    /*public function test_prompt_set_thumbnail_first_image()
    {
        Prompt::get()
              ->each(function ($prompt) {
                  $prompt->count()->create();
              });
    }*/

    /*public function test_shuffle_prompt_user_id()
    {
        $random = function () {
            return rand(1, 13);
        };
        Prompt::where('prompt_type_code', 'image')
              ->get()
              ->each(function ($prompt) use ($random) {
                  $prompt->user_id = $random();
                  $prompt->save();
              });
    }*/

    /*public function test_re_calculate_user_count()
    {
        User::with(['prompts.count', 'count'])
            ->get()
            ->each(function ($user) {
                $user->count->generated = $user->prompts->sum('count.generated');
                $user->count->save();
            });
    }*/
}
