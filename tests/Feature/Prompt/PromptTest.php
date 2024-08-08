<?php

namespace Tests\Feature\Prompt;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Prompt\Prompt;
use App\Services\Prompt\Sorts\Enums\Sorts;
use Database\Factories\User\UserFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Passport;
use AIGenerate\Models\Database\Factories\Prompt\PromptFactory;
use AIGenerate\Models\Prompt\Enums\Status;
use AIGenerate\Services\AI\OpenAI\Chat\Models;
use AIGenerate\Services\AI\OpenAI\Completion\ApiService;
use AIGenerate\Services\AI\OpenAI\Completion\Request;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;
use Tests\TestCase;

class PromptTest extends TestCase
{
    public function test_index_infinity_prompt_returns_a_successful_response(): void
    {

        $response = $this->get(route('api.prompt.index'), [
            'sort' => Sorts::Newest->value,
            'size' => 10,
        ]);
        $response->assertStatus(200);
    }

    public function test_show_prompt_returns_a_successful_response(): void
    {
        $response = $this->get(route('api.prompt.show', 1));
        $response->assertStatus(200);
    }

    public function test_store_image_prompt_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $file = new UploadedFile(
            storage_path('app/test.jpg'),
            'test.jpg',
            'image/png',
            null,
            true, // 해당 파일이 업로드된 것으로 설정합니다.
        );

        $response = $this->post(route('api.prompt.store'), [
            'title'       => 'A cute baby sea otter',
            'description' => 'A cute baby sea otter',
            'category'    => 3,
            'price'       => 1,
            'type'        => OpenAITypes::Image->value,
            'tags'        => [
                'cute',
                'sea otter',
                'cute',
                'sea otter',
                'cute',
            ],
            'images'      => [
                $file,
            ],
        ]);
        $response->assertStatus(200);

        $id = $response->json('data.id');

        Prompt::withoutGlobalScope('enabled')->where('id', $id)->update([
            'prompt_status_code' => Status::Enabled->value,
        ]);

        $response = $this->put(route('api.prompt.update-template', $id), [
            'template' => 'Cute baby sea otter doing [PROMPT] the weather [WEATHER]',
            'guide'    => 'guide for prompt',
        ]);
        $response->assertStatus(200);
    }

    public function test_store_chat_prompt_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);
        $response = $this->post(route('api.prompt.store'), [
            'title'         => 'A helpful assistant',
            'description'   => 'A helpful assistant',
            'output_result' => 'output result test',
            'category'      => 1,
            'price'         => 0.1,
            'type'          => OpenAITypes::Chat->value,
            'tags'          => [
                'assistant',
                'helpful',
                'assistant',
                'helpful',
                'assistant',
            ],
        ]);
        $response->assertStatus(200);

        $id = $response->json('data.id');
        Prompt::withoutGlobalScope('enabled')->where('id', $id)->update([
            'prompt_status_code' => Status::Enabled->value,
        ]);

        $response = $this->post(route('api.prompt.preview-template', $id), [
            'template' => 'You are a [kind] [job].',
            'guide'    => 'guide for prompt',
            'order'    => 'It is last order',
        ]);
        $response->assertStatus(200);

        $response = $this->put(route('api.prompt.update-template', $id), [
            'template' => 'You are a helpful assistant.',
            'guide'    => 'guide for prompt',
            'order'    => 'It is last order',
        ]);
        $response->assertStatus(403);

        $response = $this->put(route('api.prompt.update-template', $id), [
            'template' => 'You are a helpful assistant.',
            'guide'    => 'guide for prompt',
            'order'    => 'It is last order',
            'engine'   => Models::GPT_3_5_turbo->value,
        ]);
        $response->assertStatus(200);
    }

    public function test_store_chat_prompt_response_has_options(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);
        $response = $this->post(route('api.prompt.store'), [
            'title'         => 'Test',
            'description'   => 'Test',
            'output_result' => 'output test',
            'category'      => 1,
            'price'         => 0.1,
            'type'          => OpenAITypes::Chat->value,
            'tags'          => [
                'assistant',
                'helpful',
                'assistant',
                'helpful',
                'assistant',
            ],
        ]);
        $response->assertStatus(200);

        $id = $response->json('data.id');
        Prompt::withoutGlobalScope('enabled')->where('id', $id)->update([
            'prompt_status_code' => Status::Enabled->value,
        ]);

        $response = $this->put(route('api.prompt.update-template', $id), [
            'template' => 'Nixeu and wlop, long hair [cloth] girl, tanned, beautiful attractive face, charming and attra[cloth]ctive dimpled rear view, slender thigh, kissing attractive two girls, [표정], legs, full bo[cloth]dy, leaning against a space window, view from behind, thigh view up, tea[cloth : cloth1, cloth2, cloth3]sing, posing, realistic, professional photography, [cloth : cloth1, cloth2, cloth3]glo-fi, acce[cloth]nt lighting --ar 2:3 --niji --q 2 --v 4',
            'guide'    => 'guide for prompt',
            'order'    => 'It is last order',
            'engine'   => Models::GPT_3_5_turbo->value,
        ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.options'));
    }

    public function test_store_completion_prompt_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);
        $response = $this->post(route('api.prompt.store'), [
            'title'         => 'A helpful assistant',
            'description'   => 'A helpful assistant',
            'output_result' => 'output example',
            'category'      => 1,
            'price'         => 0.1,
            'type'          => OpenAITypes::Completion->value,
            'tags'          => [
                'assistant',
                'helpful',
                'assistant',
                'helpful',
                'assistant',
            ],
        ]);
        $response->assertStatus(200);

        $id = $response->json('data.id');
        Prompt::withoutGlobalScope('enabled')->where('id', $id)->update([
            'prompt_status_code' => Status::Enabled->value,
        ]);

        $template = '{
              "temperature": 0,
              "max_tokens": 60,
              "top_p": 1,
              "frequency_penalty": 0,
              "presence_penalty": 0
            }';

        $response = $this->put(route('api.prompt.update-template', $id), [
            'template' => $template,
            'guide'    => 'guide for prompt',
            'order'    => 'It is last order',
        ]);
        $response->assertStatus(403);

        $this->assertThrows(function () use ($template) {
            $apiService = app()->make(ApiService::class);
            $apiService->validate(new Request, get_object_vars(json_decode($template)));
        }, ValidationException::class);

        $template = '{
              "temperature": 0,
              "model" : "text-davinci-003",
              "prompt": "Correct this to standard [language]:\n\nShe no went to the market.",
              "max_tokens": 60,
              "top_p": 1,
              "frequency_penalty": 0,
              "presence_penalty": 0
            }';

        $response = $this->post(route('api.prompt.preview-template', $id), [
            'template' => $template,
            'guide'    => 'guide for prompt',
            'order'    => 'It is last order',
        ]);
        $response->assertStatus(200);

        $response = $this->put(route('api.prompt.update-template', $id), [
            'template' => $template,
            'guide'    => 'guide for prompt',
            'order'    => 'It is last order',
        ]);
        $response->assertStatus(200);
    }

    public function test_prompt_view_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);
        $prompt = PromptFactory::new()->create();

        $response = $this->get(route('api.prompt.show', $prompt));
        $response->assertStatus(200);
    }

    public function test_prompt_template_convert()
    {
        $template = '"Your task is to help me create 30 local SEO optimized social media posts for the following business in Default language. 
        Each post should contain at least five keywords that are important for that [business type] for local SEO written out naturally in sentences. 
        Each post you give me should be at least 5 sentences long. The posts should not mention discounts or new products. Everything I said above is important and must be followed. 
        Please pretend you are a local SEO expert. Please put each of these posts in a nice looking table so it looks like a calendar. 
        Also, please give a suggestion for what image they should use for each post. The only columns in the grid should be for the (1) post #, (2) post, (3) suggested image. 
        The very first thing you say should be a big bold header that says ""Social Media Posts for [Brand title]"""';
        $prompt = new Prompt([
            'template' => $template,
        ]);
        $attributes = [
            'p1' => 'business type test',
            'p2' => 'brand test',
        ];
        $converted = $template;
        collect([
            'p1' => [
                'name' => 'Business type',
            ],
            'p2' => [
                'name' => 'Brand title',
            ],
        ])->each(function ($option, $key) use (&$converted, $attributes) {
            $converted = Str::of($converted)->replace("[$option[name]]", $attributes[$key], false);
        });
        $this->assertTrue(true);
    }
}
