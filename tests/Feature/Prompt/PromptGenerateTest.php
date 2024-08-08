<?php

namespace Tests\Feature\Prompt;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use AIGenerate\Models\Database\Factories\Prompt\PromptFactory;
use AIGenerate\Models\Prompt\Enums\Status;
use AIGenerate\Services\AI\OpenAI\Chat\Models;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;
use App\Services\Prompt\PromptChatGenerateService;
use App\Services\Prompt\PromptCompletionGenerateService;
use App\Services\Prompt\PromptImageGenerateService;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PromptGenerateTest extends TestCase
{
    public function test_image_prompt_build_input_is_correct(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $service = app()->make(PromptImageGenerateService::class);

        $prompt = PromptFactory::new([
            'title'              => 'A cute baby sea otter',
            'prompt_type_code'   => OpenAITypes::Image->value,
            'prompt_engine_code' => 'default',
            'prompt_status_code' => Status::Test->value,
            'price_per_generate' => '1.1',
            'description'        => 'A cute baby sea otter',
            'template'           => 'Cute baby sea otter doing [PROMPT] the weather [WEATHER]',
        ])->create();

        $option1 = $prompt->options()->create([
            'name' => 'WEATHER',
        ]);
        $option2 = $prompt->options()->create([
            'name' => 'PROMPT',
        ]);

        $attributes = [
            'p' . $option1->getKey() => 'sunny',
            'p' . $option2->getKey() => 'eating apple',
        ];

        $input = $service->buildInput($prompt, $attributes);
        $this->assertEquals([
            "model"  => "default",
            "prompt" => "Cute baby sea otter doing eating apple the weather sunny",
        ], $input);
    }

    public function test_chat_prompt_build_input_is_correct(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $service = app()->make(PromptChatGenerateService::class);

        $prompt = PromptFactory::new([
            'title'              => 'You are the teacher of some language',
            'prompt_type_code'   => OpenAITypes::Chat->value,
            'prompt_engine_code' => Models::GPT_3_5_turbo->value,
            'prompt_status_code' => Status::Test->value,
            'price_per_generate' => '1.1',
            'description'        => 'You are the teacher of some language',
            'template'           => 'You are the teacher of [language].',
        ])->create();

        $prompt->options()->create([
            'name'  => 'user',
            'value' => 'What kind of students do you teach?',
        ]);
        $prompt->options()->create([
            'name'  => 'assistant',
            'value' => 'I am a teacher teaching young elementary school students',
        ]);

        $option1 = $prompt->options()->create([
            'name' => 'language',
        ]);

        $attributes = [
            'p' . $option1->getKey() => 'korean',
            'language'               => 'english',
            'tone'                   => 'cold',
            'writing_style'          => 'descriptive',
        ];

        $input = $service->buildInput($prompt, $attributes);
        $this->assertEquals([
            "model"    => "gpt-3.5-turbo",
            "messages" => [
                [
                    'role'    => 'system',
                    'content' => 'You are the teacher of korean.',
                ],
                [
                    'role'    => 'user',
                    'content' => 'What kind of students do you teach?',
                ],
                [
                    'role'    => 'assistant',
                    'content' => 'I am a teacher teaching young elementary school students',
                ],
                [
                    "role"    => "user",
                    "content" => '
Write all in english. Please write in english tone. descriptive writing style.',
                ],
            ],
        ], $input);
    }

    public function test_completion_prompt_build_input_is_correct(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $service = app()->make(PromptCompletionGenerateService::class);

        $prompt = PromptFactory::new([
            'title'              => 'Correct this to standard English',
            'prompt_type_code'   => OpenAITypes::Completion->value,
            'prompt_engine_code' => \AIGenerate\Services\AI\OpenAI\Completion\Models::TEXT_DAVINCI_003->value,
            'prompt_status_code' => Status::Test->value,
            'price_per_generate' => '1.1',
            'description'        => 'Correct this to standard English',
            'template'           => '{
                  "model": "text-davinci-003",
                  "prompt": "Correct this to standard [language]:\n\nShe no went to the market.",
                  "temperature": 0,
                  "max_tokens": 60,
                  "top_p": 1,
                  "frequency_penalty": 0,
                  "presence_penalty": 0
                }',
        ])->create();

        $option1 = $prompt->options()->create([
            'name' => 'language',
        ]);

        $attributes = [
            'p' . $option1->getKey() => 'korean',
            'language'               => 'english',
            'tone'                   => 'cold',
            'writing_style'          => 'descriptive',
        ];

        $input = $service->buildInput($prompt, $attributes);
        $this->assertEquals([
            "model"             => "text-davinci-003",
            "prompt"            => [
                0 => "Correct this to standard korean:\n
She no went to the market.
Write all in english. Please write in english tone. descriptive writing style.",
            ],
            "temperature"       => 0,
            "max_tokens"        => 60,
            "top_p"             => 1,
            "frequency_penalty" => 0,
            "presence_penalty"  => 0,
        ], $input);
    }

}
