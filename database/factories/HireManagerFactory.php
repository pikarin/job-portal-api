<?php

namespace Database\Factories;

use App\Models\HireManager;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HireManager>
 */
class HireManagerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HireManager::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => UserFactory::new(),
            'name' => $this->faker->name,
            'location' => $this->faker->city,
            'company_name' => $this->faker->company,
            'description' => $this->faker->sentence,
        ];
    }
}
