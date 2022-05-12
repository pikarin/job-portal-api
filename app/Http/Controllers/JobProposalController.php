<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Proposal;
use App\Http\Resources\ProposalResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\JsonResource;

class JobProposalController extends Controller
{
    public function index(Job $job): JsonResource
    {
        $proposals = $job->proposals()->with('freelancer.user')->paginate();

        return ProposalResource::collection($proposals);
    }

    public function update(Job $job, Proposal $proposal, string $status): JsonResponse
    {
        $this->abortIfJobNotValid($job);

        $proposal->update([
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Proposal accepted successfully',
        ], 201);
    }

    /**
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function abortIfJobNotValid(Job $job): void
    {
        if ($job->hire_manager_id != auth()->user()->hireManager->id) {
            throw new AuthenticationException;
        }

        if ($job->status != 'published') {
            throw ValidationException::withMessages([
                'job_id' => ['Job is not published'],
            ]);
        }
    }
}
