<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Factories\FreelancerFactory;
use Database\Factories\UserFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function freelancers_can_see_their_profile()
    {
        $user = $this->actingAsFreelancer();

        $response = $this->getJson('/api/profile/freelancer');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->freelancer->name,
                'description' => $user->freelancer->description,
                'location' => $user->freelancer->location,
            ],
        ]);
    }

    /** @test */
    public function freelancers_can_add_skills_to_their_profile()
    {
        $this->seed(SkillTableSeeder::class);

        $user = $this->actingAsFreelancer();

        $skillIds = [1, 2, 3];

        $response = $this->postJson('/api/freelancer/skills', [
            'skills' => $skillIds,
        ]);

        $response->assertStatus(201);

        foreach ($skillIds as $skillId) {
            $this->assertDatabaseHas('freelancer_skill', [
                'freelancer_id' => $user->freelancer->id,
                'skill_id' => $skillId,
            ]);
        }
    }

    /** @test */
    public function hire_managers_can_see_their_profile()
    {
        $user = $this->actingAsHireManager();

        $response = $this->getJson('/api/profile/hire-manager');

        $response->assertStatus(200);
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
    }

    protected function actingAsFreelancer(array $userAttributes = [], array $freelancerAttributes = []): User
    {
        $user = UserFactory::new()->freelancer($freelancerAttributes)->create($userAttributes);

        Sanctum::actingAs($user);

        return $user;
    }

    protected function actingAsHireManager(array $userAttributes = [], array $hireManagerAttributes = []): User
    {
        $user = UserFactory::new()->hireManager($hireManagerAttributes)->create($userAttributes);

        Sanctum::actingAs($user);

        return $user;
    }
}
