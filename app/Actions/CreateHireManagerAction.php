<?php

namespace App\Actions;

use App\DataTransferObjects\HireManagerData;
use App\Models\HireManager;

class CreateHireManagerAction
{
    public function execute(HireManagerData $hireManagerData): HireManager
    {
        return HireManager::create([
            'user_id' => $hireManagerData->user_id,
            'name' => $hireManagerData->name,
            'location' => $hireManagerData->location,
            'description' => $hireManagerData->description,
            'company_name' => $hireManagerData->company_name,
        ]);
    }
}
