<?php

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;

use Tests\TestCase;
use App\Models\User;
use Database\Factories\UserFactory;
use Database\Factories\FreelancerFactory;
use Database\Factories\HireManagerFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('freelancers can register using their email', function ()
{
    $registrationData = freelancerData();

    $response = postJson('/api/register/freelancers', $registrationData)->assertStatus(201);

    $response->assertJson([
        'message' => 'Successfully registered',
    ]);

    assertDatabaseHas('users', [
        'email' => $registrationData['email'],
    ]);

    assertDatabaseHas('freelancers', [
        'name' => $registrationData['name'],
        'location' => $registrationData['location'],
        'description' => $registrationData['description'],
    ]);
});

test('freelancers cannot register using registered email', function ()
{
    FreelancerFactory::new()
        ->for(UserFactory::new()->state([
            'email' => 'existing_email@example.com',
        ]))
        ->create();

    $registrationData = freelancerData([
        'email' => 'existing_email@example.com'
    ]);

    $response = postJson('/api/register/freelancers', $registrationData)->assertStatus(422);

    $response->assertJsonValidationErrors('email');

    assertDatabaseMissing('freelancers', [
        'name' => $registrationData['name'],
        'location' => $registrationData['location'],
        'description' => $registrationData['description'],
    ]);
});

test('freelancers can regiser using their hire manager account', function ()
{
    HireManagerFactory::new()
        ->for(UserFactory::new()->state([
            'email' => 'existing_email@example.com',
        ]))
        ->create();

    $user = User::first();

    $registrationData = freelancerData([
        'email' => 'existing_email@example.com',
    ]);

    $response = postJson(
        "/api/register/freelancers/{$user->id}",
        $registrationData,
    );

    $response->assertStatus(201);
    assertDatabaseHas('freelancers', [
        'user_id' => $user->id,
        'name' => $registrationData['name'],
        'location' => $registrationData['location'],
        'description' => $registrationData['description'],
    ]);
});

test('cannot register as freelancer if user already a freelancer', function ()
{
    FreelancerFactory::new()
        ->for(UserFactory::new()->state([
            'email' => 'existing@example.com',
        ]))
        ->create();

        $user = User::first();

        $registrationData = freelancerData([
            'email' => 'existing@example.com',
        ]);

        $response = postJson(
            "/api/register/freelancers/{$user->id}",
            $registrationData,
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user']);
});

test('hire managers can register using their email', function ()
{
    $registrationData = hireManagerData();

    $response = postJson('/api/register/hire-managers', $registrationData)->assertStatus(201);

    $response->assertJson([
        'message' => 'Successfully registered',
    ]);

    assertDatabaseHas('users', [
        'email' => $registrationData['email'],
    ]);

    assertDatabaseHas('hire_managers', [
        'name' => $registrationData['name'],
        'location' => $registrationData['location'],
        'description' => $registrationData['description'],
        'company_name' => $registrationData['company_name'],
    ]);
});

test('hire managers cannot register using registered email', function ()
{
    HireManagerFactory::new()
        ->for(UserFactory::new()->state([
            'email' => 'existing_email@example.com',
        ]))
        ->create();

    $registrationData = hireManagerData([
        'email' => 'existing_email@example.com',
    ]);

    $response = postJson('/api/register/hire-managers', $registrationData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('email');

    assertDatabaseMissing('hire_managers', [
        'name' => $registrationData['name'],
        'location' => $registrationData['location'],
        'description' => $registrationData['description'],
        'company_name' => $registrationData['company_name'],
    ]);
});

test('hire managers can regiser using their freelancer account', function ()
{
    FreelancerFactory::new()
        ->for(UserFactory::new()->state([
            'email' => 'existing_email@example.com',
        ]))
        ->create();

    $user = User::first();

    $registrationData = hireManagerData([
        'email' => 'existing_email@example.com',
    ]);

    $response = postJson("/api/register/hire-managers/{$user->id}", $registrationData);

    $response->assertStatus(201);
    assertDatabaseHas('hire_managers', [
        'user_id' => $user->id,
        'name' => $registrationData['name'],
        'location' => $registrationData['location'],
        'description' => $registrationData['description'],
        'company_name' => $registrationData['company_name'],
    ]);
});

test('cannot register as hire manager if user already a hire manager', function ()
{
    HireManagerFactory::new()
        ->for(UserFactory::new()->state([
            'email' => 'existing@example.com',
        ]))
        ->create();

        $user = User::first();

        $registrationData = hireManagerData([
            'email' => 'existing@example.com',
        ]);

        $response = postJson(
            "/api/register/hire-managers/{$user->id}",
            $registrationData,
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['user']);
});

function freelancerData(?array $overrides = []): array
{
    return array_merge([
        'email' => 'freelancer@example.com',
        'password' => 'secretpassword',
        'password_confirmation' => 'secretpassword',
        'name' => 'New Freelancer',
        'location' => 'Bandung',
        'timezone' => 'Asia/Jakarta',
        'description' => 'I am a freelancer',
    ], $overrides);
}

function hireManagerData(?array $overrides = []): array
{
    return array_merge([
        'email' => 'hire_manager@example.com',
        'password' => 'securepassword',
        'password_confirmation' => 'securepassword',
        'name' => 'New Hire Manager',
        'location' => 'Singapore',
        'timezone' => 'Asia/Singapore',
        'description' => 'I am a hire manager',
        'company_name' => 'Acme Inc',
    ], $overrides);
}
