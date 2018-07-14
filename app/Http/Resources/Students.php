<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Grades;
use App\Http\Resources\Classes;

class Students extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->load('class');
        return [
            'Id'=> $this->id,
            'StudentId' => $this->student_id,
            'Name' => $this->name,
            'Dob' =>$this->dob,
            'Gender' => $this->gender,
            'Status'=> $this->status,
        ];
    }
}
