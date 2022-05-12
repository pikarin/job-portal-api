<?php

namespace App\DataTransferObjects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserData
{
    public function __construct(
        public string $email,
        public string $password,
        public ?int $id = null
    ) {}

    public function hashedPassword(): string
    {
        return Hash::make($this->password);
    }
}
