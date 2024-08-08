<?php

namespace Tests\Feature\User;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Prompt\PromptGenerateService;
use Carbon\Carbon;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use AIGenerate\Models\Database\Factories\Prompt\PromptFactory;
use AIGenerate\Models\Database\Factories\Prompt\PromptGenerateFactory;
use Tests\TestCase;

class PromptTest extends TestCase
{
    public function test_prompt_generate_list_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $response = $this->get(route('api.user.prompt.list.generate'));
        $response->assertStatus(200);
    }

    public function test_prompt_create_list_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $response = $this->get(route('api.user.prompt.list.create'));
        $response->assertStatus(200);
    }

    public function test_prompt_selling_list_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $response = $this->get(route('api.user.prompt.list.selling'));
        $response->assertStatus(200);
    }

    public function test_prompt_point_list_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $response = $this->get(route('api.user.point.index'));
        $response->assertStatus(200);
    }

    public function test_selling_prompt_price_returns_correct(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);
        $prompt = PromptFactory::new()->create([
            'user_id'            => $user->getKey(),
            'price_per_generate' => 1,
        ]);
        $user->prompts()->save($prompt);

        PromptGenerateFactory::new()->create([
            'prompt_id'  => $prompt->getKey(),
            'user_id'    => $user->getKey(),
            'price'      => 1.1,
            'created_at' => Carbon::create(2023, 1, 1),
        ]);

        PromptGenerateFactory::new()->create([
            'prompt_id'  => $prompt->getKey(),
            'user_id'    => $user->getKey(),
            'price'      => 1.1,
            'created_at' => Carbon::create(2023, 1, 2),
        ]);
        PromptGenerateFactory::new()->create([
            'prompt_id'  => $prompt->getKey(),
            'user_id'    => $user->getKey(),
            'price'      => 1.1,
            'created_at' => Carbon::create(2023, 2, 1),
        ]);

        $service = app()->make(PromptGenerateService::class);
        $start = Carbon::create(2023, 1, 1);
        $end = Carbon::create(2023, 1, 31);
        $price = $service->getSalesPrice($user, $start, $end);

        $this->assertEquals(2.2, $price);
    }

    public function test_selling_prompt_list_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);
        $prompt = PromptFactory::new()->create([
            'user_id'            => $user->getKey(),
            'price_per_generate' => 1,
        ]);
        $user->prompts()->save($prompt);

        PromptGenerateFactory::new()->create([
            'prompt_id'  => $prompt->getKey(),
            'user_id'    => $user->getKey(),
            'price'      => 1.1,
            'created_at' => Carbon::create(2023, 1, 1),
        ]);

        PromptGenerateFactory::new()->create([
            'prompt_id'  => $prompt->getKey(),
            'user_id'    => $user->getKey(),
            'price'      => 1.1,
            'created_at' => Carbon::create(2023, 1, 2),
        ]);
        PromptGenerateFactory::new()->create([
            'prompt_id'  => $prompt->getKey(),
            'user_id'    => $user->getKey(),
            'price'      => 1.1,
            'created_at' => Carbon::create(2023, 2, 1),
        ]);

        $response = $this->get(
            route('api.user.prompt.list.selling', [
                'start' => '2023-01-01',
                'end'   => '2023-01-31',
            ]),
        );

        $response->assertStatus(200);
    }
}
