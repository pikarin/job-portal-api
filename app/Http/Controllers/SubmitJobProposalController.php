<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubmitJobProposalController extends Controller
{
    public function __invoke(Job $job)
    {
        $this->abortIfProposalNotValid($job);

        $job->proposals()->create([
            'freelancer_id' => auth()->id(),
            'status' => 'sent',
            'payment_amount' => $job->payment_amount,
        ]);

        return response()->json([
            'message' => 'Proposal sent successfully',
        ], 201);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function abortIfProposalNotValid(Job $job): void
    {
        if ($job->status != 'published') {
            throw ValidationException::withMessages([
                'job_id' => ['Job is not published'],
            ]);
        }

        if ($job->hire_manager_id == auth()->user()->hireManager?->id) {
            throw ValidationException::withMessages([
                'job_id' => ['Cannot apply to this job'],
            ]);
        }

        $proposalExists = Proposal::query()
            ->where('job_id', $job->id)
            ->where('freelancer_id', auth()->id())
            ->exists();

        if ($proposalExists) {
            throw ValidationException::withMessages([
                'job_id' => ['You have already submitted a proposal for this job.'],
            ]);
        }
    }
}
