<?php

namespace Tests\Feature\Prompt;

use App\Services\Point\Enums\Types;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use AIGenerate\Services\AI\OpenAI\Chat\ApiService;
use AIGenerate\Services\AI\OpenAI\Chat\Models;
use Tests\TestCase;

class ChatPromptGenerateTest extends TestCase
{
    // use RefreshDatabase;

    public function test_chat_prompt_service(): void
    {
        /**
         * @var \AIGenerate\Services\AI\OpenAI\Chat\ApiService $service
         */
        $service = app(ApiService::class);
        $response = $service->call([
            'model'    => Models::GPT_3_5_turbo->value,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are a helpful assistant.',
                ],
                [
                    'role'    => 'user',
                    'content' => 'Who won the world series in 2020?',
                ],
                [
                    'role'    => 'assistant',
                    'content' => 'Where was it played?',
                ],
                [
                    'role'    => 'user',
                    'content' => 'The 2020 World Series was played at Globe Life Field in Arlington, Texas.',
                ],
            ],
        ]);

        /*
        array:6 [
          "id" => "chatcmpl-74n7Xk34clYA0Fj4PQix4ztucI2tY"
          "object" => "chat.completion"
          "created" => 1681376487
          "model" => "gpt-3.5-turbo-0301"
          "choices" => array:1 [
            0 => array:3 [
              "index" => 0
              "message" => array:2 [
                "role" => "assistant"
                "content" => "The Los Angeles Dodgers won the 2020 World Series. They defeated the Tampa Bay Rays in six games to win their first championship since 1988."
              ]
              "finish_reason" => "stop"
            ]
          ]
          "usage" => array:3 [
            "prompt_tokens" => 61
            "completion_tokens" => 31
            "total_tokens" => 92
          ]
        ]
         */
        $this->assertIsString($response['id']);
        $this->assertIsString($response['object']);
        $this->assertIsInt($response['created']);
        $this->assertIsString($response['model']);
        $this->assertIsArray($response['choices']);
        $this->assertIsArray($response['usage']);
    }

    public function test_store_prompt_generate_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        $user->pointHistories()->create([
            'point_type_code' => Types::Plus->value,
            'point'           => 2,
            'remained'        => 2,
            'description'     => 'test',
        ]);
        Passport::actingAs($user);
        $response = $this->post(route('api.prompt.generate.chat', [2]), [
            'order'      => 'Where was it played?',
            'max_tokens' => 16,
        ]);
        //        dump($response->getContent());
        $response->assertStatus(200);
    }

    public function test_store_prompt_generate_using_template_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        $user->pointHistories()->create([
            'point_type_code' => Types::Plus->value,
            'point'           => 2,
            'remained'        => 2,
            'description'     => 'test',
        ]);
        Passport::actingAs($user);
        $response = $this->post(route('api.prompt.generate.chat', [3]), [
            'p5'         => 'japaneses',
            'order'      => 'How do you say sushi in Japanese?',
            'max_tokens' => 16,
        ]);
        //        dump($response->getContent());
        $response->assertStatus(200);
    }
}
