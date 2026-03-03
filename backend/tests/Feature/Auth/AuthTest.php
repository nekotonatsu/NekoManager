<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistUser(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email']
            ]);
    }

    public function testLogin(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email']
            ]);
    }

    public function testLoginFailed(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422);
    }

    public function testAuthenticatedUserGetOwnInfo(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    public function testAuthenticatedUserNotAccessMe(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }
}