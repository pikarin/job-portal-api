<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Job::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'hire_manager_id' => HireManagerFactory::new(),
            'status' => 'draft',
            'title' => $this->faker->sentence,
        ];
    }

    public function published(array $attributes = []): self
    {
        $attributes = array_merge([
            'description' => $this->faker->sentence,
            'complexity' => $this->faker->sentence,
            'duration' => $this->faker->sentence,
            'payment_amount' => $this->faker->numberBetween(100, 1000),
        ], $attributes);

        return $this->state($attributes)->state(['status' => 'published']);
    }

    public function drafted(array $attributes = []): self
    {
        return $this->state($attributes)->state(['status' => 'draft']);
    }
}
