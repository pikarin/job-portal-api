<?php

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('freelancers can see their profile', function ()
{
    $user = actingAsFreelancer();

    $response = getJson('/api/profile/freelancer')->assertStatus(200);

    $response->assertJson([
        'data' => [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->freelancer->name,
            'description' => $user->freelancer->description,
            'location' => $user->freelancer->location,
        ],
    ]);
});

test('freelancers can add skills to their profile', function ()
{
    seed(SkillTableSeeder::class);

    $user = actingAsFreelancer();

    $skillIds = [1, 2, 3];

    $response = postJson('/api/freelancer/skills', [
        'skills' => $skillIds,
    ]);

    $response->assertStatus(201);

    foreach ($skillIds as $skillId) {
        assertDatabaseHas('freelancer_skill', [
            'freelancer_id' => $user->freelancer->id,
            'skill_id' => $skillId,
        ]);
    }
});

test('hire managers can see their profile', function ()
{
    $user = actingAsHireManager();

    $response = getJson('/api/profile/hire-manager')->assertStatus(200);

    $response->assertJson([
        'data' => [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->hireManager->name,
            'description' => $user->hireManager->description,
            'location' => $user->hireManager->location,
            'company_name' => $user->hireManager->company_name,
        ],
    ]);
});
