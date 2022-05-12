<?php

namespace Tests\Feature;

use Database\Factories\HireManagerFactory;
use Database\Factories\JobFactory;
use Database\Factories\ProposalFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProposalJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(SkillTableSeeder::class);
    }

    /** @test */
    public function freelancers_can_apply_to_published_jobs()
    {
        $user = $this->actingAsFreelancer();

        $job = JobFactory::new()->published()->create();

        $response = $this->postJson("/api/jobs/{$job->id}/proposals");
        $response->assertStatus(201);

        $this->assertDatabaseHas('proposals', [
            'job_id' => $job->id,
            'freelancer_id' => $user->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function freelancers_cannot_apply_multiple_proposals_to_same_job()
    {
        $user = $this->actingAsFreelancer();

        $job = JobFactory::new()->published()->create();
        ProposalFactory::new()->sent()->create([
            'job_id' => $job->id,
            'freelancer_id' => $user->id
        ]);

        $response = $this->postJson("/api/jobs/{$job->id}/proposals");
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('job_id');
    }

    /** @test */
    public function freelancers_cannot_apply_to_draft_jobs()
    {
        $this->actingAsFreelancer();

        $job = JobFactory::new()->drafted()->create();

        $response = $this->postJson("/api/jobs/{$job->id}/proposals");
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('job_id');
    }

    /** @test */
    public function freelancers_cannot_apply_to_their_own_jobs()
    {
        $user = $this->actingAsFreelancer();

        $job = JobFactory::new()
            ->published()
            ->for(HireManagerFactory::new(['user_id' => $user->id]))
            ->create();

        $response = $this->postJson("/api/jobs/{$job->id}/proposals");
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('job_id');
    }

    /** @test */
    public function hire_managers_can_view_proposals_from_freelancer_for_their_job_post()
    {
        $user = $this->actingAsHireManager();

        $job = JobFactory::new()
            ->published()
            ->has(ProposalFactory::new()->count(3))
            ->create([
                'hire_manager_id' => $user->hireManager->id,
            ]);

        $response = $this->getJson("/api/jobs/{$job->id}/proposals");
        $response->assertStatus(200);

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
    }

    /** @test */
    public function hire_managers_can_accept_proposals_from_freelancer_for_their_job_post()
    {
        $user = $this->actingAsHireManager();

        $proposal = ProposalFactory::new()
            ->sent()
            ->for(JobFactory::new()->published()->state([
                'hire_manager_id' => $user->hireManager->id,
            ]))
            ->create();

        $response = $this->putJson("/api/jobs/$proposal->job_id/proposals/$proposal->id/accepted");
        $response->assertStatus(201);

        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'accepted',
        ]);
    }

    /** @test */
    public function hire_managers_can_finish_proposals_from_freelancer_for_their_job_post()
    {
        $user = $this->actingAsHireManager();

        $proposal = ProposalFactory::new()
            ->accepted()
            ->for(JobFactory::new()->published()->state([
                'hire_manager_id' => $user->hireManager->id,
            ]))
            ->create();

        $response = $this->putJson("/api/jobs/$proposal->job_id/proposals/$proposal->id/finished");
        $response->assertStatus(201);

        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'finished',
        ]);
    }

    /** @test */
    public function hire_managers_can_reject_proposals_from_freelancer_for_their_job_post()
    {
        $user = $this->actingAsHireManager();

        $proposal = ProposalFactory::new()
            ->sent()
            ->for(JobFactory::new()->published()->state([
                'hire_manager_id' => $user->hireManager->id,
            ]))
            ->create();

        $response = $this->putJson("/api/jobs/$proposal->job_id/proposals/$proposal->id/rejected");
        $response->assertStatus(201);

        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'rejected',
        ]);
    }
}
