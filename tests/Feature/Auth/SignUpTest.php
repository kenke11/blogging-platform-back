<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SignUpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function sign_successfully()
    {
        Role::create(['name' => 'admin']);

        $response = $this->postJson(route('auth.signup'), [
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response->assertStatus(201);

        $user = User::where('email', 'admin@gmail.com')->first();
        $this->assertNotNull($user);

        $this->assertEquals('admin', $user->name);
        $this->assertEquals('admin@gmail.com', $user->email);

        $this->assertTrue($user->hasRole('admin'));
    }

    /**
     * @test
     */
    public function sign_validations()
    {
        Role::create(['name' => 'admin']);

        $response = $this->postJson(route('auth.signup'), [
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response->assertStatus(201);

        $response = $this->postJson(route('auth.signup'), [
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' =>  "The name has already been taken.",
                'email' => "The email has already been taken."
            ]);

        $response = $this->postJson(route('auth.signup'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'role' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' => 'The name field is required.',
                'email' => 'The email field is required.',
                'role' => 'The role field is required.',
                'password' => 'The password field is required.',
            ]);

        $response = $this->postJson(route('auth.signup'), [
            'name' => '',
            'email' => 'qwd',
            'password' => '',
            'role' => 'wrongRole',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' => 'The name field is required.',
                'email' => 'The email must be a valid email address.',
                'role' => 'The selected role is invalid.',
                'password' => 'The password field is required.',
            ]);
    }
}
