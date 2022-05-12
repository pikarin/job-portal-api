<?php

namespace App\DataTransferObjects;

class FreelancerData
{
    public function __construct(
        public int $user_id,
        public string $name,
        public string $location,
        public string $description,
    ) {}
}
