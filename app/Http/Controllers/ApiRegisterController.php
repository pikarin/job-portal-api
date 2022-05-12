<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterFreelancerRequest;
use App\Http\Requests\RegisterHireManagerRequest;
use App\Http\Requests\StoreFreelancerRequest;
use App\Http\Requests\StoreHireManagerRequest;
use App\Http\Requests\UpdateToHireManagerRequest;
use Illuminate\Validation\ValidationException;

class ApiRegisterController extends Controller
{
    public function freelancer(RegisterFreelancerRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->freelancer()->create([
                'name' => $request->name,
                'location' => $request->location,
                'description' => $request->description,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    public function freelancerUpdate(StoreFreelancerRequest $request, User $user)
    {
        if ($user->freelancer()->exists()) {
            throw ValidationException::withMessages([
                'user' => ['You are already a freelancer'],
            ]);
        }

        $user->freelancer()->create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    public function hireManager(RegisterHireManagerRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->hireManager()->create([
                'name' => $request->name,
                'location' => $request->location,
                'description' => $request->description,
                'company_name' => $request->company_name,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    public function hireManagerUpdate(StoreHireManagerRequest $request, User $user)
    {
        if ($user->hireManager()->exists()) {
            throw ValidationException::withMessages([
                'user' => ['You are already a hire manager'],
            ]);
        }

        $user->hireManager()->create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'company_name' => $request->company_name,
        ]);

        return response()->json(['message' => 'Successfully registered'], 201);
    }
}
