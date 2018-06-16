<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Classes;

class Grades extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->load('classes');
        return [
            'Id'=>$this->id,
            'Name' => $this->name,
            'Class'=> Classes::collection($this->whenLoaded('classes')),];
    }
}
