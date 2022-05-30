<?php

namespace App\Actions;

use App\DataTransferObjects\JobData;
use App\Models\Job;

class CreateJobAction
{
    public function execute(JobData $jobData): Job
    {
        /** @var \App\Models\Job $job */
        $job = Job::create([
            'hire_manager_id' => $jobData->hire_manager_id,
            'title' => $jobData->title,
            'status' => $jobData->status,
            'description' => $jobData->description,
            'complexity' => $jobData->complexity,
            'duration' => $jobData->duration,
            'payment_amount' => $jobData->payment_amount,
        ]);

        if (!empty($jobData->skills)) {
            $job->skills()->attach($jobData->skills);
        }

        return $job;
    }
}
