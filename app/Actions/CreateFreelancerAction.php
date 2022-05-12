<?php

namespace App\Actions;

use App\DataTransferObjects\FreelancerData;
use App\Models\Freelancer;

class CreateFreelancerAction
{
    public function execute(FreelancerData $freelancerData): Freelancer
    {
        return Freelancer::create([
            'user_id' => $freelancerData->user_id,
            'name' => $freelancerData->name,
            'location' => $freelancerData->location,
            'description' => $freelancerData->description,
        ]);
    }
}
