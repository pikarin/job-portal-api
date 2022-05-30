<?php

use function Pest\Laravel\postJson;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user_can_login_to_get_access_token', function ()
{
    UserFactory::new()->create([
        'email' => 'aditia@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = postJson('/api/login', [
        'email' => 'aditia@example.com',
        'password' => 'secret123',
        'device_name' => 'MyDevice',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'token',
    ]);
});

test('cannot_login_using_invalid_credential', function ()
{
    UserFactory::new()->create([
        'email' => 'aditia@example.com',
    ]);

    $response = postJson('/api/login', [
        'email' => 'wrong_email@wrong-domain.com',
        'password' => 'wrong_password',
        'device_name' => 'MyDevice',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['email']);
});
