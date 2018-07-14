<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Course;

class GradePlan extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->load('courses');
        return [
            'Id' => $this->id,
            'Name' => $this->name,
            'Courses'=>Course::collection($this->whenLoaded('courses')),
        ];
    }
}
