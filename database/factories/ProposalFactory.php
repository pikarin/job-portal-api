<?php

namespace Database\Factories;

use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Proposal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'job_id' => JobFactory::new(),
            'freelancer_id' => FreelancerFactory::new(),
            'payment_amount' => $this->faker->numberBetween(100, 1000),
            'status' => 'sent',
        ];
    }

    public function sent(): self
    {
        return $this->state(['status' => 'sent']);
    }

    public function accepted(): self
    {
        return $this->state(['status' => 'accepted']);
    }

    public function finished(): self
    {
        return $this->state(['status' => 'finished']);
    }

    public function rejected(): self
    {
        return $this->state(['status' => 'rejected']);
    }
}
