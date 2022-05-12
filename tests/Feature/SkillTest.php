<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Database\Seeders\SkillTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_users_can_see_available_skills()
    {
        $this->seed(SkillTableSeeder::class);

        Sanctum::actingAs(UserFactory::new()->create());

        $response = $this->getJson('/api/skills');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);
    }
}
