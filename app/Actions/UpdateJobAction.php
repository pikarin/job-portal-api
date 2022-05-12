<?php

namespace App\Actions;

use App\DataTransferObjects\JobData;
use App\Models\Job;

class UpdateJobAction
{
    public function execute(JobData $jobData, Job $job): Job
    {
        $job->update([
            'title' => $jobData->title,
            'status' => $jobData->status,
            'description' => $jobData->description,
            'complexity' => $jobData->complexity,
            'duration' => $jobData->duration,
            'payment_amount' => $jobData->payment_amount,
        ]);

        if (!empty($jobData->skills)) {
            $job->skills()->sync($jobData->skills);
        }

        return $job;
    }
}
