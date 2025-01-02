<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
    }

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createCustomer(array $attributes = []): User
    {
        return $this->createUser(array_merge([
            'role' => 'customer'
        ], $attributes));
    }

    protected function createAdmin(array $attributes = []): User
    {
        return $this->createUser(array_merge([
            'role' => 'admin'
        ], $attributes));
    }

    protected function loginAs(User $user)
    {
        $token = auth()->login($user);
        return $token;
    }

    protected function getAuthHeader(string $token): array
    {
        return ['Authorization' => 'Bearer ' . $token];
    }
}
