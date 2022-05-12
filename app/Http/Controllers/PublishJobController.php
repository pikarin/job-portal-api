<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Actions\CreateJobAction;
use App\Actions\UpdateJobAction;
use App\Http\Resources\JobResource;
use App\DataTransferObjects\JobData;
use App\Http\Requests\PublishJobRequest;
use Illuminate\Auth\AuthenticationException;

class PublishJobController extends Controller
{
    public function store(PublishJobRequest $request, CreateJobAction $createJobAction)
    {
        $job = $createJobAction->execute(JobData::fromPublishRequest($request));

        return JobResource::make($job->loadMissing('skills'));
    }

    public function update(
        PublishJobRequest $request,
        UpdateJobAction $updateJobAction,
        Job $job,
    ) {
        if ($job->hire_manager_id != auth()->user()->hireManager->id) {
            throw new AuthenticationException;
        }

        $job = $updateJobAction->execute(
            JobData::fromPublishRequest($request),
            $job,
        );

        return JobResource::make($job->loadMissing('skills'));
    }
}
