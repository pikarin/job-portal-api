<?php

use function Pest\Laravel\getJson;
use function Pest\Laravel\seed;

use Database\Factories\UserFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated users can see available skills', function ()
{
    seed(SkillTableSeeder::class);

    actingAsFreelancer();

    $response = getJson('/api/skills')->assertStatus(200);

    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'name',
            ],
        ],
    ]);
});
