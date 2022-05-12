<?php

namespace App\Actions;

use App\Models\User;
use App\DataTransferObjects\UserData;

class CreateUserAction
{
    public function execute(UserData $userData): User
    {
        return User::create([
            'email' => $userData->email,
            'password' => $userData->hashedPassword(),
        ]);
    }
}
