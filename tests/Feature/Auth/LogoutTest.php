<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function user_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('authToken')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('auth.logout'));

        $response->assertStatus(200)
            ->assertJson(['message' => 'User logged out successfully']);

        $this->assertNull(PersonalAccessToken::findToken($token));
    }

    /**
     * @test
     */
    public function unauthorized_user_cannot_logout()
    {
        $response = $this->postJson(route('auth.logout'));

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
