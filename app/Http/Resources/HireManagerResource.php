<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\User $resource
 */
class HireManagerResource extends JsonResource
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
            'email' => $this->resource->email,
            'name' => $this->resource->hireManager->name,
            'location' => $this->resource->hireManager->location,
            'description' => $this->resource->hireManager->description,
            'company_name' => $this->resource->hireManager->company_name,
        ];
    }
}
