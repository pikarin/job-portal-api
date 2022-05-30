<?php

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

use Database\Factories\JobFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

uses(RefreshDatabase::class);

test('hire managers can draft new jobs', function ()
{
    $user = actingAsHireManager();

    $response = postJson('/api/jobs/draft', [
        'title' => 'Laravel Developer (Remote)',
    ]);

    $response->assertStatus(201);
    $response->assertJson([
        'data' => [
            'id' => 1,
            'title' => 'Laravel Developer (Remote)',
        ],
    ]);

    assertDatabaseHas('jobs', [
        'hire_manager_id' => $user->hireManager->id,
        'title' => 'Laravel Developer (Remote)',
        'status' => 'draft',
    ]);
});

test('hire managers can publish new jobs', function ()
{
    seed(SkillTableSeeder::class);

    $user = actingAsHireManager();

    $jobData = jobData([
        'skills' => [1, 2, 3, 5],
    ]);

    $response = postJson('/api/jobs/publish', $jobData)->assertStatus(201);

    $response->assertJson([
        'data' => [
            'id' => 1,
            'title' => $jobData['title'],
            'status' => 'published',
            'description' => $jobData['description'],
            'complexity' => $jobData['complexity'],
            'duration' => $jobData['duration'],
            'payment_amount' => $jobData['payment_amount'],
            'skills' => [
                ['id' => 1, 'name' => 'PHP'],
                ['id' => 2, 'name' => 'Laravel'],
                ['id' => 3, 'name' => 'MySQL'],
                ['id' => 5, 'name' => 'Javascript'],
            ],
        ],
    ]);

    assertDatabaseHas('jobs', [
        'hire_manager_id' => $user->hireManager->id,
        'title' => $jobData['title'],
        'status' => 'published',
        'description' => $jobData['description'],
        'complexity' => $jobData['complexity'],
        'duration' => $jobData['duration'],
        'payment_amount' => $jobData['payment_amount'],
    ]);

    foreach ($jobData['skills'] as $skillId) {
        assertDatabaseHas('job_skill', [
            'job_id' => 1,
            'skill_id' => $skillId,
        ]);
    }
});

test('hire managers can published their draft jobs', function ()
{
    seed(SkillTableSeeder::class);

    $user = actingAsHireManager();

    $job = JobFactory::new()->drafted()->create([
        'hire_manager_id' => $user->hireManager->id,
    ]);

    $response = postJson("/api/jobs/publish/{$job->id}", jobData([
        'title' => 'Published Job',
        'skills' => [1, 3, 4],
    ]));

    $response->assertStatus(200);

    assertDatabaseHas('jobs', [
        'id' => $job->id,
        'title' => 'Published Job',
    ]);
});

test('hire managers can update their draft jobs', function ()
{
    seed(SkillTableSeeder::class);

    $user = actingAsHireManager();

    $job = JobFactory::new()->drafted()->create([
        'hire_manager_id' => $user->hireManager->id,
    ]);

    $response = postJson("/api/jobs/draft/{$job->id}", jobData([
        'title' => 'Drafted Job',
        'skills' => [1, 3, 4],
    ]));

    $response->assertStatus(200);

    assertDatabaseHas('jobs', [
        'id' => $job->id,
        'title' => 'Drafted Job',
    ]);
});

test('hire managers cannot published other hire managers jobs', function ()
{
    seed(SkillTableSeeder::class);

    actingAsHireManager();

    $job = JobFactory::new()->drafted()->create();

    $response = postJson("/api/jobs/publish/{$job->id}", jobData([
        'skills' => [1]
    ]));

    $response->assertUnauthorized();
});

test('freelancers cannot draft new jobs', function ()
{
    actingAsFreelancer();

    $response = postJson('/api/jobs/draft', [
        'title' => 'Job From Freelancer',
    ]);

    $response->assertUnauthorized();

    assertDatabaseMissing('jobs', [
        'title' => 'Job From Freelancer',
    ]);
});

test('freelancers cannot publish new jobs', function ()
{
    actingAsFreelancer();

    $response = postJson('/api/jobs/publish', jobData([
        'title' => 'Published Job From Freelancer',
    ]));

    $response->assertUnauthorized();

    assertDatabaseMissing('jobs', [
        'title' => 'Published Job From Freelancer',
    ]);
});

function jobData(array $overrides = []): array
{
    return array_merge([
        'title' => 'Company Landing Page',
        'description' => 'A landing page for a company.',
        'complexity' => 'medium',
        'duration' => '2 weeks',
        'payment_amount' => '1000000',
    ], $overrides);
}
