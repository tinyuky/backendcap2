<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Grades;

class Classes extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'Id'=> $this->id,
            'Name' => $this->name,
            'Grade' => Grades::collection($this->whenLoaded('grades')),
        ];
    }
}
