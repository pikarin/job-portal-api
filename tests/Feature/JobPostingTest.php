<?php

namespace Tests\Feature;

use Database\Factories\JobFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function hire_managers_can_draft_new_jobs()
    {
        $user = $this->actingAsHireManager();

        $response = $this->postJson('/api/jobs/draft', [
            'title' => 'Laravel Developer (Remote)',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => 'Laravel Developer (Remote)',
            ],
        ]);

        $this->assertDatabaseHas('jobs', [
            'hire_manager_id' => $user->hireManager->id,
            'title' => 'Laravel Developer (Remote)',
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function hire_managers_can_publish_new_jobs()
    {
        $this->seed(SkillTableSeeder::class);

        $user = $this->actingAsHireManager();

        $jobData = $this->jobData([
            'skills' => [1, 2, 3, 5],
        ]);

        $response = $this->postJson('/api/jobs/publish', $jobData);

        $response->assertStatus(201);
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

        $this->assertDatabaseHas('jobs', [
            'hire_manager_id' => $user->hireManager->id,
            'title' => $jobData['title'],
            'status' => 'published',
            'description' => $jobData['description'],
            'complexity' => $jobData['complexity'],
            'duration' => $jobData['duration'],
            'payment_amount' => $jobData['payment_amount'],
        ]);

        foreach ($jobData['skills'] as $skillId) {
            $this->assertDatabaseHas('job_skill', [
                'job_id' => 1,
                'skill_id' => $skillId,
            ]);
        }
    }

    /** @test */
    public function hire_managers_can_published_their_draft_jobs()
    {
        $this->seed(SkillTableSeeder::class);

        $user = $this->actingAsHireManager();

        $job = JobFactory::new()->drafted()->create([
            'hire_manager_id' => $user->hireManager->id,
        ]);

        $response = $this->post("/api/jobs/publish/{$job->id}", $this->jobData([
            'title' => 'Published Job',
            'skills' => [1, 3, 4],
        ]));

        $response->assertStatus(200);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Published Job',
        ]);
    }

    /** @test */
    public function hire_managers_can_update_their_draft_jobs()
    {
        $this->seed(SkillTableSeeder::class);

        $user = $this->actingAsHireManager();

        $job = JobFactory::new()->drafted()->create([
            'hire_manager_id' => $user->hireManager->id,
        ]);

        $response = $this->post("/api/jobs/draft/{$job->id}", $this->jobData([
            'title' => 'Drafted Job',
            'skills' => [1, 3, 4],
        ]));

        $response->assertStatus(200);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Drafted Job',
        ]);
    }

    /** @test */
    public function hire_managers_cannot_published_other_hire_managers_jobs()
    {
        $this->seed(SkillTableSeeder::class);

        $this->actingAsHireManager();

        $job = JobFactory::new()->drafted()->create();

        $response = $this->postJson("/api/jobs/publish/{$job->id}", $this->jobData([
            'skills' => [1]
        ]));

        $response->assertUnauthorized();
    }

    /** @test */
    public function freelancers_cannot_draft_new_jobs()
    {
        $this->actingAsFreelancer();

        $response = $this->postJson('/api/jobs/draft', [
            'title' => 'Job From Freelancer',
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseMissing('jobs', [
            'title' => 'Job From Freelancer',
        ]);
    }

    /** @test */
    public function freelancers_cannot_publish_new_jobs()
    {
        $this->actingAsFreelancer();

        $response = $this->postJson('/api/jobs/publish', $this->jobData([
            'title' => 'Published Job From Freelancer',
        ]));

        $response->assertUnauthorized();

        $this->assertDatabaseMissing('jobs', [
            'title' => 'Published Job From Freelancer',
        ]);
    }

    protected function jobData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Company Landing Page',
            'description' => 'A landing page for a company.',
            'complexity' => 'medium',
            'duration' => '2 weeks',
            'payment_amount' => '1000000',
        ], $overrides);
    }
}
