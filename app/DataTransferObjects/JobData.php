<?php

namespace App\DataTransferObjects;

use App\Http\Requests\DraftJobRequest;
use App\Http\Requests\PublishJobRequest;
use Illuminate\Http\Request;

class JobData
{
    public function __construct(
        public int $hire_manager_id,
        public string $title,
        public string $status,
        public ?string $description,
        public ?string $complexity,
        public ?string $duration,
        public ?string $payment_amount,
        public array $skills = [],
    ) {}

    public static function fromDraftRequest(DraftJobRequest $request): self
    {
        return static::fromRequest($request, 'draft');
    }

    public static function fromPublishRequest(PublishJobRequest $request): self
    {
        return static::fromRequest($request, 'published');
    }

    public static function fromRequest(Request $request, string $status): self
    {
        return new self(
            hire_manager_id: $request->user()->hireManager->id,
            title: $request->title,
            status: $status,
            description: $request->description,
            complexity: $request->complexity,
            duration: $request->duration,
            payment_amount: $request->payment_amount,
            skills: $request->get('skills', []),
        );
    }
}
