<?php

namespace Tests\Feature\Prompt;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Point\Enums\Types;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use AIGenerate\Services\AI\OpenAI\Completion\ApiService;
use AIGenerate\Services\AI\OpenAI\Completion\Models;
use Tests\TestCase;

class CompletionPromptGenerateTest extends TestCase
{
    public function test_completion_prompt_service(): void
    {
        /**
         * @var ApiService $service
         */
        $service = app(ApiService::class);
        $response = $service->call([
            'model'       => Models::TEXT_DAVINCI_003->value,
            'prompt'      => [
                'I am a highly intelligent question answering bot. If you ask me a question that is rooted in truth, I will give you the answer. If you ask me a question that is nonsense, trickery, or has no clear answer, I will respond with \"Unknown\".\n\nQ: What is human life expectancy in the United States?\nA: Human life expectancy in the United States is 78 years.\n\nQ: Who was president of the United States in 1955?\nA: Dwight D. Eisenhower was president of the United States in 1955.\n\nQ: Which party did he belong to?\nA: He belonged to the Republican Party.\n\nQ: What is the square root of banana?\nA: Unknown\n\nQ: How does a telescope work?\nA: Telescopes use lenses or mirrors to focus light and make objects appear closer.\n\nQ: Where were the 1992 Olympics held?\nA: The 1992 Olympics were held in Barcelona, Spain.\n\nQ: How many squigs are in a bonk?\nA: Unknown\n\nQ:When summer started?',
            ],
            'temperature' => 0,
            'max_tokens'  => 100,
            'top_p'       => 1,
        ]);
        /*
        array:6 [
          "id" => "cmpl-75A96aWLKOAYXPD1UAKjGJwyAOlgn"
          "object" => "text_completion"
          "created" => 1681464996
          "model" => "text-davinci-003"
          "choices" => array:1 [
            0 => array:4 [
              "text" => """
                \n
                \n
                A: Summer in the Northern Hemisphere typically starts on June 20th or 21st.
                """
              "index" => 0
              "logprobs" => null
              "finish_reason" => "stop"
            ]
          ]
          "usage" => array:3 [
            "prompt_tokens" => 251
            "completion_tokens" => 19
            "total_tokens" => 270
          ]
        ]
        */
        //        dump($response);
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
        $response = $this->post(route('api.prompt.generate.completion', [4]), [
            'p6'    => 'japanese',
            'order' => 'She no went to the market.',
        ]);
        //        dump($response->getContent());
        $response->assertStatus(200);
    }

}
