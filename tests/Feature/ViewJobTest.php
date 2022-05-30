<?php

use function Pest\Laravel\getJson;
use function Pest\Laravel\seed;

use Database\Factories\JobFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


test('freelancers can view published jobs', function ()
{
    seed(SkillTableSeeder::class);

    actingAsFreelancer();

    JobFactory::new()->published()->count(50)->create();

    $response = getJson('/api/jobs')->assertStatus(200);

    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'title',
                'status',
                'description',
                'created_at',
                'updated_at',
            ]
        ],
        'links' => [
            'first',
            'last',
            'prev',
            'next',
        ],
        'meta' => [
            'current_page',
            'from',
            'last_page',
            'path',
            'per_page',
            'to',
            'total',
        ],
    ]);
});


test('hire managers can view all their jobs', function ()
{
    $this->seed(SkillTableSeeder::class);

    $user = actingAsHireManager();

    $otherJob = JobFactory::new()->published()->create([
        'title' => 'Job from other hire manager',
    ]);

    JobFactory::new()->published()->count(20)->create([
        'hire_manager_id' => $user->hireManager->id,
    ]);

    $response = getJson('/api/jobs/hire-manager')->assertStatus(200);

    $response->assertDontSee($otherJob->title);

    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'title',
                'status',
                'description',
                'created_at',
                'updated_at',
            ]
        ],
        'links' => [
            'first',
            'last',
            'prev',
            'next',
        ],
        'meta' => [
            'current_page',
            'from',
            'last_page',
            'path',
            'per_page',
            'to',
            'total',
        ],
    ]);
});
