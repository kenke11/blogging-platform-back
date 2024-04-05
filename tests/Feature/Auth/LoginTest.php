<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function login_successfully()
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create([
             'email' => 'admin@gmail.com'
        ]);
        $user->assignRole('admin');

        $response = $this->postJson(route('auth.login'), [
            'email' => 'admin@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function login_with_incorrect_credentials()
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create([
            'email' => 'admin@gmail.com'
        ]);
        $user->assignRole('admin');

        $response = $this->postJson(route('auth.login'), [
            'email' => 'adminnnnn@gmail.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'admin@gmail.com',
            'password' => 'passworddddd'
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function login_validations()
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create([
            'email' => 'admin@gmail.com'
        ]);
        $user->assignRole('admin');

        $response = $this->postJson(route('auth.login'), [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => 'The email field is required.',
                'password' => 'The password field is required.'
            ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'admin',
            'password' => '123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => 'The email must be a valid email address.',
                'password' => 'The password must be at least 8 characters.'
            ]);
    }

    /**
     * @test
     */
    public function it_returns_authenticated_user_data()
    {
        Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('authToken')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('user.get_auth_user'));

        $response->assertStatus(200)
            ->assertJsonStructure(['user' => [
                'id',
                'name',
                'email',
                'role',
            ]]);

        $userData = $response->json('user');
        $this->assertEquals($user->id, $userData['id']);
        $this->assertEquals($user->name, $userData['name']);
        $this->assertEquals($user->email, $userData['email']);
        $this->assertEquals('admin', $userData['role']);
    }

    /**
     * @test
     */
    public function it_returns_error_when_user_unauthenticated()
    {
        $response = $this->getJson(route('user.get_auth_user'));

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
