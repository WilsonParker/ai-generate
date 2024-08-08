<?php

namespace Tests\Feature\User;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_view_returns_a_successful_response(): void
    {
        $from = UserFactory::new()->create();
        $to = UserFactory::new()->create();
        Passport::actingAs($from);

        $response = $this->get(route('api.user.show', $to));
        $response->assertStatus(200);
    }

}
