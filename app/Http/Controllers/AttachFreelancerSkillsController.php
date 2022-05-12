<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttachFreelancerSkillsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'skills' => ['required', 'array'],
            'skills.*' => ['required', 'exists:skills,id'],
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->freelancer->skills()->sync($request->skills);

        return response()->json([
            'message' => 'Skills saved successfully',
        ], 201);
    }
}
