<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Student_In_ClassInPlan_Rs extends JsonResource
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
            'StudentId' => $this->student_id,
            'Name' => $this->name,
            'Dob' =>$this->dob,
            'Gender' => $this->gender,
            'Status'=> $this->status,
            // 'Class' => $this->whenLoaded('class')->name,
            'InClass' => $this->inclass,
        ];
    }
}
