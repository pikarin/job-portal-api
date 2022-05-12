<?php

namespace App\Http\Controllers;

use App\Http\Resources\FreelancerResource;
use App\Http\Resources\HireManagerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function freelancer()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->load('freelancer');

        if (!$user->freelancer) {
            return $this->notFoundResponse('You are not a freelancer');
        }

        return FreelancerResource::make($user);
    }

    public function hireManager()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->load('hireManager');

        if (!$user->hireManager) {
            return $this->notFoundResponse('You are not a hire manager');
        }

        return HireManagerResource::make($user);
    }

    protected function notFoundResponse(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 404);
    }
}
