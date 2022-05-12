<?php

namespace App\DataTransferObjects;

class HireManagerData
{
    public function __construct(
        public int $user_id,
        public string $name,
        public string $location,
        public string $description,
        public string $company_name,
    ) {}
}
