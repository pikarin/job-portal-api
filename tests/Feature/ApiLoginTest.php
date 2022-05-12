<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_to_get_access_token()
    {
        UserFactory::new()->create([
            'email' => 'aditia@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'aditia@example.com',
            'password' => 'secret123',
            'device_name' => 'MyDevice',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
    }

    /** @test */
    public function cannot_login_using_invalid_credential()
    {
        UserFactory::new()->create([
            'email' => 'aditia@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong_email@wrong-domain.com',
            'password' => 'wrong_password',
            'device_name' => 'MyDevice',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
