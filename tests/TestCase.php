<?php

namespace Tests;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

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
