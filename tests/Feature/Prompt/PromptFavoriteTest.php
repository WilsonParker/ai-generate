<?php

namespace Tests\Feature\Prompt;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PromptFavoriteTest extends TestCase
{
    public function test_favorite_list_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $response = $this->get(route('api.prompt.index'));
        $response->assertStatus(200);
    }

    public function test_add_favorite_prompt_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);

        $response = $this->post(route('api.prompt.favorite.store', 1));
        $response->assertStatus(200);
    }

}
