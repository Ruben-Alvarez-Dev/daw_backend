<?php

namespace Tests\Feature\Api\V1;

use Tests\Feature\Api\ApiTestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends ApiTestCase
{
    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ],
                'token'
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'role' => 'customer'
                ]
            ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        $existingUser = $this->createCustomer([
            'email' => 'existing@example.com'
        ]);

        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login()
    {
        $password = 'password123';
        $user = $this->createCustomer([
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ]
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = $this->createCustomer();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ]);
    }

    public function test_user_can_logout()
    {
        $user = $this->createCustomer();
        $token = $this->loginAs($user);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
    }

    public function test_user_can_refresh_token()
    {
        $user = $this->createCustomer();
        $token = $this->loginAs($user);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->postJson('/api/v1/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in'
            ]);
    }

    public function test_user_can_get_profile()
    {
        $user = $this->createCustomer();
        $token = $this->loginAs($user);

        $response = $this->withHeaders($this->getAuthHeader($token))
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}
