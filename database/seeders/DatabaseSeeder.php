<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\JobFactory;
use Database\Factories\FreelancerFactory;
use Database\Factories\HireManagerFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SkillTableSeeder::class);

        HireManagerFactory::new()->count(10)->create();
        FreelancerFactory::new()->count(10)->create();

        JobFactory::new()
            ->count(10)
            ->state(new Sequence(
                ['status' => 'draft'],
                ['status' => 'published'],
            ))
            ->create();
    }
}
