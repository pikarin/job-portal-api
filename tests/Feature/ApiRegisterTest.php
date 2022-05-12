<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Factories\UserFactory;
use Database\Factories\FreelancerFactory;
use Database\Factories\HireManagerFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function freelancers_can_register_using_their_email()
    {
        $registrationData = $this->freelancerData();

        $response = $this->postJson('/api/register/freelancers', $registrationData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Successfully registered',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $registrationData['email'],
        ]);

        $this->assertDatabaseHas('freelancers', [
            'name' => $registrationData['name'],
            'location' => $registrationData['location'],
            'description' => $registrationData['description'],
        ]);
    }

    /** @test */
    public function freelancers_cannot_register_using_registered_email()
    {
        FreelancerFactory::new()
            ->for(UserFactory::new()->state([
                'email' => 'existing_email@example.com',
            ]))
            ->create();

        $registrationData = $this->freelancerData([
            'email' => 'existing_email@example.com'
        ]);

        $response = $this->postJson('/api/register/freelancers', $registrationData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');

        $this->assertDatabaseMissing('freelancers', [
            'name' => $registrationData['name'],
            'location' => $registrationData['location'],
            'description' => $registrationData['description'],
        ]);
    }

    /** @test */
    public function freelancers_can_regiser_using_their_hire_manager_account()
    {
        HireManagerFactory::new()
            ->for(UserFactory::new()->state([
                'email' => 'existing_email@example.com',
            ]))
            ->create();

        $user = User::first();

        $registrationData = $this->freelancerData([
            'email' => 'existing_email@example.com',
        ]);

        $response = $this->postJson(
            "/api/register/freelancers/{$user->id}",
            $registrationData,
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('freelancers', [
            'user_id' => $user->id,
            'name' => $registrationData['name'],
            'location' => $registrationData['location'],
            'description' => $registrationData['description'],
        ]);
    }

    /** @test */
    public function cannot_register_as_freelancer_if_user_already_a_freelancer()
    {
        FreelancerFactory::new()
            ->for(UserFactory::new()->state([
                'email' => 'existing@example.com',
            ]))
            ->create();

            $user = User::first();

            $registrationData = $this->freelancerData([
                'email' => 'existing@example.com',
            ]);

            $response = $this->postJson(
                "/api/register/freelancers/{$user->id}",
                $registrationData,
            );

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['user']);
    }

    /** @test */
    public function hire_managers_can_register_using_their_email()
    {
        $registrationData = $this->hireManagerData();

        $response = $this->postJson('/api/register/hire-managers', $registrationData);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Successfully registered',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $registrationData['email'],
        ]);

        $this->assertDatabaseHas('hire_managers', [
            'name' => $registrationData['name'],
            'location' => $registrationData['location'],
            'description' => $registrationData['description'],
            'company_name' => $registrationData['company_name'],
        ]);
    }

    /** @test */
    public function hire_managers_cannot_register_using_registered_email()
    {
        HireManagerFactory::new()
            ->for(UserFactory::new()->state([
                'email' => 'existing_email@example.com',
            ]))
            ->create();

        $registrationData = $this->hireManagerData([
            'email' => 'existing_email@example.com',
        ]);

        $response = $this->postJson('/api/register/hire-managers', $registrationData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');

        $this->assertDatabaseMissing('hire_managers', [
            'name' => $registrationData['name'],
            'location' => $registrationData['location'],
            'description' => $registrationData['description'],
            'company_name' => $registrationData['company_name'],
        ]);
    }

    /** @test */
    public function hire_managers_can_regiser_using_their_freelancer_account()
    {
        FreelancerFactory::new()
            ->for(UserFactory::new()->state([
                'email' => 'existing_email@example.com',
            ]))
            ->create();

        $user = User::first();

        $registrationData = $this->hireManagerData([
            'email' => 'existing_email@example.com',
        ]);

        $response = $this->postJson("/api/register/hire-managers/{$user->id}", $registrationData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('hire_managers', [
            'user_id' => $user->id,
            'name' => $registrationData['name'],
            'location' => $registrationData['location'],
            'description' => $registrationData['description'],
            'company_name' => $registrationData['company_name'],
        ]);
    }

    /** @test */
    public function cannot_register_as_hire_manager_if_user_already_a_hire_manager()
    {
        HireManagerFactory::new()
            ->for(UserFactory::new()->state([
                'email' => 'existing@example.com',
            ]))
            ->create();

            $user = User::first();

            $registrationData = $this->hireManagerData([
                'email' => 'existing@example.com',
            ]);

            $response = $this->postJson(
                "/api/register/hire-managers/{$user->id}",
                $registrationData,
            );

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['user']);
    }

    protected function freelancerData(?array $overrides = []): array
    {
        return array_merge([
            'email' => 'freelancer@example.com',
            'password' => 'secretpassword',
            'password_confirmation' => 'secretpassword',
            'name' => 'New Freelancer',
            'location' => 'Bandung',
            'description' => 'I am a freelancer',
        ], $overrides);
    }

    protected function hireManagerData(?array $overrides = []): array
    {
        return array_merge([
            'email' => 'hire_manager@example.com',
            'password' => 'securepassword',
            'password_confirmation' => 'securepassword',
            'name' => 'New Hire Manager',
            'location' => 'Jakarta',
            'description' => 'I am a hire manager',
            'company_name' => 'Acme Inc',
        ], $overrides);
    }
}
