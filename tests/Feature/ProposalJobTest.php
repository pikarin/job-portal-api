<?php

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\seed;

use Database\Factories\HireManagerFactory;
use Database\Factories\JobFactory;
use Database\Factories\ProposalFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => seed(SkillTableSeeder::class));

test('freelancers can apply to published jobs', function ()
{
    $user = actingAsFreelancer();

    $job = JobFactory::new()->published()->create();

    postJson("/api/jobs/{$job->id}/proposals")->assertStatus(201);

    assertDatabaseHas('proposals', [
        'job_id' => $job->id,
        'freelancer_id' => $user->id,
        'status' => 'sent',
    ]);
});

test('freelancers cannot apply multiple proposals to same job', function ()
{
    $user = actingAsFreelancer();

    $job = JobFactory::new()->published()->create();

    ProposalFactory::new()->sent()->create([
        'job_id' => $job->id,
        'freelancer_id' => $user->id
    ]);

    $response = postJson("/api/jobs/{$job->id}/proposals")->assertStatus(422);

    $response->assertJsonValidationErrors('job_id');
});

test('freelancers cannot apply to draft jobs', function ()
{
    actingAsFreelancer();

    $job = JobFactory::new()->drafted()->create();

    $response = postJson("/api/jobs/{$job->id}/proposals")->assertStatus(422);

    $response->assertJsonValidationErrors('job_id');
});

test('freelancers cannot apply to their own jobs', function ()
{
    $user = actingAsFreelancer();

    $job = JobFactory::new()
        ->published()
        ->for(HireManagerFactory::new(['user_id' => $user->id]))
        ->create();

    $response = postJson("/api/jobs/{$job->id}/proposals")->assertStatus(422);

    $response->assertJsonValidationErrors('job_id');
});

test('hire managers can view proposals for their job post', function ()
{
    $user = actingAsHireManager();

    $job = JobFactory::new()
        ->published()
        ->has(ProposalFactory::new()->count(3))
        ->create([
            'hire_manager_id' => $user->hireManager->id,
        ]);

    $response = $this->getJson("/api/jobs/{$job->id}/proposals")->assertStatus(200);

    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'job_id',
                'status',
                'payment_amount',
                'freelancer' => [
                    'id',
                    'name',
                    'email',
                ],
            ]
        ]
    ]);
});

test('hire managers can accept proposals for their job post', function ()
{
    $user = actingAsHireManager();

    $proposal = ProposalFactory::new()
        ->sent()
        ->for(JobFactory::new()->published()->state([
            'hire_manager_id' => $user->hireManager->id,
        ]))
        ->create();

    $response = putJson("/api/jobs/$proposal->job_id/proposals/$proposal->id/accepted");
    $response->assertStatus(201);

    assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'accepted',
    ]);
});

test('hire managers can finish proposals from their accepted proposals', function ()
{
    $user = actingAsHireManager();

    $proposal = ProposalFactory::new()
        ->accepted()
        ->for(JobFactory::new()->published()->state([
            'hire_manager_id' => $user->hireManager->id,
        ]))
        ->create();

    $response = putJson("/api/jobs/$proposal->job_id/proposals/$proposal->id/finished");
    $response->assertStatus(201);

    assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'finished',
    ]);
});

test('hire managers can reject proposals for their job post', function ()
{
    $user = actingAsHireManager();

    $proposal = ProposalFactory::new()
        ->sent()
        ->for(JobFactory::new()->published()->state([
            'hire_manager_id' => $user->hireManager->id,
        ]))
        ->create();

    $response = putJson("/api/jobs/$proposal->job_id/proposals/$proposal->id/rejected");
    $response->assertStatus(201);

    assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'rejected',
    ]);
});
