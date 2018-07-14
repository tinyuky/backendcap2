<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Students;

class Logs extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->load('students');
        return [
            'Id' => $this->id,
            'Name' => $this->name,
            'Date' => $this->created_at->toDateString(),
            'Students' => Students::collection($this->whenLoaded('students')),
        ];
    }
}
