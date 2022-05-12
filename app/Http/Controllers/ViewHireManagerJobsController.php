<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViewHireManagerJobsController extends Controller
{
    public function __invoke()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $jobs = $user->hireManager->jobs()->paginate();

        return JsonResource::collection($jobs);
    }
}
