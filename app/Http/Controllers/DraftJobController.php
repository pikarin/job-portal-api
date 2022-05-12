<?php

namespace App\Http\Controllers;

use App\Actions\CreateJobAction;
use App\Actions\UpdateJobAction;
use App\Http\Resources\JobResource;
use App\DataTransferObjects\JobData;
use App\Http\Requests\DraftJobRequest;
use App\Models\Job;

class DraftJobController extends Controller
{
    public function store(DraftJobRequest $request, CreateJobAction $createJobAction)
    {
        $job = $createJobAction->execute(JobData::fromDraftRequest($request));

        return JobResource::make($job);
    }

    public function update(
        DraftJobRequest $request,
        UpdateJobAction $updateJobAction,
        Job $job,
    ) {
        $job = $updateJobAction->execute(JobData::fromDraftRequest($request), $job);

        return JobResource::make($job);
    }
}
