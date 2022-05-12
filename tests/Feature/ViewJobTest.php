<?php

namespace Tests\Feature;

use Database\Factories\JobFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_view_published_jobs()
    {
        $this->seed(SkillTableSeeder::class);

        JobFactory::new()->published()->count(50)->create();

        $response = $this->get('/api/jobs');
        $response->assertStatus(200);

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
    }

    /** @test */
    public function hire_managers_can_view_all_their_jobs()
    {
        $this->seed(SkillTableSeeder::class);

        $user = $this->actingAsHireManager();

        $otherJob = JobFactory::new()->published()->create([
            'title' => 'Job from other hire manager',
        ]);

        JobFactory::new()->published()->count(20)->create([
            'hire_manager_id' => $user->hireManager->id,
        ]);

        $response = $this->get('/api/jobs/hire-manager');
        $response->assertStatus(200);

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
    }
}
