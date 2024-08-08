<?php

namespace Tests\Feature\Payment;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Point\Enums\Types;
use App\Services\Point\PointCalculator;
use App\Services\Prompt\PromptChatGenerateService;
use Database\Factories\User\UserFactory;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use AIGenerate\Models\Database\Factories\Prompt\PromptFactory;
use AIGenerate\Models\Payment\PointHistory;
use AIGenerate\Models\Prompt\Enums\Status;
use AIGenerate\Models\Prompt\Prompt;
use AIGenerate\Services\AI\OpenAI\Chat\Models;
use AIGenerate\Services\AI\OpenAI\Enums\OpenAITypes;
use AIGenerate\Services\AI\OpenAI\Images\ImageSize;
use Tests\TestCase;

class PointTest extends TestCase
{
    public function test_user_point_is_correct(): void
    {
        $user = UserFactory::new()->make();
        $user->pointHistories = collect([
            new PointHistory([
                'point_type_code' => Types::Plus->value,
                'point'           => 10,
                'remained'        => 10,
            ]),
            new PointHistory([
                'point_type_code' => Types::Plus->value,
                'point'           => 5,
                'remained'        => 5,
            ]),
            new PointHistory([
                'point_type_code' => Types::Minus->value,
                'point'           => 6,
                'remained'        => 6,
            ]),
        ]);

        $calculator = app()->make(PointCalculator::class);
        $point = $calculator->getRemainedPoint($user);
        $this->assertEquals(9, $point);
    }

    public function test_chat_api_point_to_be_paid_is_correct(): void
    {
        $model = Models::GPT_3_5_turbo;

        $token = 512;
        $point = $token * $model->perToken();
        $this->assertEquals(0.001, $point);

        $token = 1024;
        $point = $token * $model->perToken();
        $this->assertEquals(0.002, $point);
    }

    public function test_image_api_point_to_be_paid_is_correct(): void
    {

        $model = ImageSize::s256;
        $size = 1;
        $point = $size * $model->perToken();
        $this->assertEquals(0.016, $point);

        $model = ImageSize::s512;
        $size = 2;
        $point = $size * $model->perToken();
        $this->assertEquals(0.036, $point);

        $model = ImageSize::s1024;
        $size = 1;
        $point = $size * $model->perToken();
        $this->assertEquals(0.020, $point);
    }

    public function test_chat_api_point_to_be_paid_is_correct_using_fake(): void
    {
        $maxToken = 100;

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'awesome!',
                        ],
                    ],
                ],
                "usage"   => [
                    "prompt_tokens"     => 10,
                    "completion_tokens" => 20,
                    "total_tokens"      => 30,
                ],
            ]),
        ]);

        $model = Models::GPT_3_5_turbo;

        $sellerPrice = 1;
        $inputPrice = $model->getMaxToken() * $model->perToken();   // 0.008
        $maxTokenPrice = $maxToken * $model->perToken();            // 0.0001953125
        $totalPrice = $sellerPrice + $inputPrice + $maxTokenPrice;  // 1.0081953125
        $this->assertEquals(1.0081953125, $totalPrice);
    }

    public function test_prompt_generate_point_to_be_paid_is_correct(): void
    {
        $service = app()->make(PointCalculator::class);

        $prompt = new Prompt([
            'price_per_generate' => 0.1,
            'prompt_type_code'   => OpenAITypes::Chat->value,
            'prompt_engine_code' => Models::GPT_3_5_turbo->value,
        ]);
        $point = $service->getExpectedPointToBePaid($prompt, 100);
        $this->assertEquals(0.10019531250000001, $point);
    }

    public function test_prompt_chat_generate_point_to_be_paid_is_correct(): void
    {
        $service = app()->make(PointCalculator::class);
        $generateService = app()->make(PromptChatGenerateService::class);

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
            'max_tokens'             => 32,
        ];

        $expectedTokens = $generateService->getExpectationTokens($prompt, $attributes);
        $this->assertEquals($expectedTokens, 106);

        $point = $service->getExpectedPointToBePaid($prompt, $expectedTokens);
        $this->assertEquals(1.10020703125, $point);
    }
}
