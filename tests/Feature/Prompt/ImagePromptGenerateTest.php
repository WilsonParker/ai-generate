<?php

namespace Tests\Feature\Prompt;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Point\Enums\Types;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use AIGenerate\Services\AI\OpenAI\Images\ApiService;
use AIGenerate\Services\AI\OpenAI\Images\ImageSize;
use AIGenerate\Services\AI\OpenAI\Images\ResponseFormat;
use Tests\TestCase;

class ImagePromptGenerateTest extends TestCase
{
    public function test_image_prompt_service(): void
    {
        /**
         * @var ApiService $service
         */
        $service = app(ApiService::class);
        $response = $service->call([
            'prompt'          => 'Cute baby sea otter doing banging clams the weather sunny',
            'n'               => 1,
            'size'            => ImageSize::s256->value,
            'response_format' => ResponseFormat::URL->value,
        ]);
        /*
        array:2 [
          "created" => 1681377388
          "data" => array:1 [
            0 => array:1 [
              "url" => "https://oaidalleapiprodscus.blob.core.windows.net/private/org-aoEz5iNLIebmbf63rluY80og/user-qu4iAXir7V7epx4GnSdnMeVz/img-jKkOsa1oAZ1UQdaRCu2lkuke.png?st=2023-04-13T08%3A16%3A28Z&se=2023-04-13T10%3A16%3A28Z&sp=r&sv=2021-08-06&sr=b&rscd=inline&rsct=image/png&skoid=6aaadede-4fb3-4698-a8f6-684d7786b067&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2023-04-13T00%3A33%3A54Z&ske=2023-04-14T00%3A33%3A54Z&sks=b&skv=2021-08-06&sig=s6bJt2U0%2B7C3DXevEo9Il59fCMlqTqJl5VkUYqHEBVs%3D"
            ]
          ]
        ]
        */
        $this->assertIsInt($response['created']);
        $this->assertIsArray($response['data']);
        $this->assertIsString($response['data'][0]['url']);
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
        $response = $this->post(route('api.prompt.generate.image', [1]), [
            'p1'   => 'sunny',
            'p2'   => 'banging clams',
            'size' => ImageSize::s256->value,
        ]);
        $response->assertStatus(200);
    }

}
