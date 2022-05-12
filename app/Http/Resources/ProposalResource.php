<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @property \App\Models\Proposal $resource */
class ProposalResource extends JsonResource
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
            'job_id' => $this->resource->job_id,
            'status' => $this->resource->status,
            'payment_amount' => (int) $this->resource->payment_amount,
            'freelancer' => new FreelancerResource($this->resource->freelancer->user),
            'created_at' => $this->resource->created_at->timezone(auth()->user()->timezone),
            'updated_at' => $this->resource->updated_at->timezone(auth()->user()->timezone),
        ];
    }
}
