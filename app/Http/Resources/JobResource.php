<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\Job $resource
 */
class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'status' => $this->resource->status,
            'description' => $this->resource->description,
            'complexity' => $this->resource->complexity,
            'duration' => $this->resource->duration,
            'payment_amount' => $this->resource->payment_amount,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'created_at' => $this->resource->created_at->timezone(auth()->user()->timezone),
            'updated_at' => $this->resource->updated_at->timezone(auth()->user()->timezone),
        ];
    }
}
