<?php

namespace Tests\Feature\User;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Factories\User\UserFactory;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_login_returns_a_successful_response(): void
    {
        $user = UserFactory::new()->create();
        Passport::actingAs($user);
        $token = auth()->user()->createToken('authToken')->accessToken;
//        dump($token);
        $this->assertIsString($token);
    }

    public function test_generate_token_returns_a_successful_response(): void
    {
        $response = $this->get(route('third.auth.generate-test-token'));
//        dump($response->getContent());
        $response->assertStatus(200);
    }

}
