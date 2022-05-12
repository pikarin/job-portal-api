<?php

namespace App\Http\Controllers;

use App\Actions\CreateFreelancerAction;
use App\Actions\CreateHireManagerAction;
use App\Actions\CreateUserAction;
use App\DataTransferObjects\FreelancerData;
use App\DataTransferObjects\HireManagerData;
use App\DataTransferObjects\UserData;
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
    public function freelancer(
        RegisterFreelancerRequest $request,
        CreateUserAction $createUserAction,
        CreateFreelancerAction $createFreelancerAction,
    ) {

        try {
            DB::beginTransaction();

            $user = $createUserAction->execute(
                new UserData(...$request->safe(['email', 'password']))
            );

            $freelancerRequestData = ['user_id' => $user->id]
                + $request->safe()->except(['email', 'password']);

            $createFreelancerAction->execute(
                new FreelancerData(...$freelancerRequestData)
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    public function freelancerUpdate(
        StoreFreelancerRequest $request,
        CreateFreelancerAction $createFreelancerAction,
        User $user,
    ) {
        if ($user->freelancer()->exists()) {
            $this->throwErrorResponse('You are already a freelancer');
        }

        $requestData = $request->validated() + ['user_id' => $user->id];

        $createFreelancerAction->execute(new FreelancerData(...$requestData));

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    public function hireManager(
        RegisterHireManagerRequest $request,
        CreateUserAction $createUserAction,
        CreateHireManagerAction $createHireManagerAction,
    ) {
        try {
            DB::beginTransaction();

            $user = $createUserAction->execute(
                new UserData(...$request->safe(['email', 'password']))
            );

            $hireManagerRequestData = ['user_id' => $user->id]
                + $request->safe()->except(['email', 'password']);

            $createHireManagerAction->execute(
                new HireManagerData(...$hireManagerRequestData)
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    public function hireManagerUpdate(
        StoreHireManagerRequest $request,
        CreateHireManagerAction $createHireManagerAction,
        User $user,
    ) {
        if ($user->hireManager()->exists()) {
            $this->throwErrorResponse('You are already a hire manager');
        }

        $requestData = $request->validated() + ['user_id' => $user->id];

        $createHireManagerAction->execute(new HireManagerData(...$requestData));

        return response()->json(['message' => 'Successfully registered'], 201);
    }

    /**
     * Throw validation exception with custom messages.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwErrorResponse(string $message)
    {
        throw ValidationException::withMessages([
            'user' => [$message],
        ]);
    }
}
