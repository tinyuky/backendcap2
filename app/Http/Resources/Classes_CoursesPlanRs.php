<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Classes_CoursesPlanRs extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->load('courseinplan');
        $this->resource->load('lecturer');
        return [
            'Id'=> $this->id,
            'Name' => $this->name,
            'CoursePlanId' => $this->courseplan_id,
            'Course' => $this->whenLoaded('courseinplan')->course->name,
            'Grade' => $this->whenLoaded('courseinplan')->course->grade->name,
            'GradeId' => $this->whenLoaded('courseinplan')->course->grade->id,
            'LecturerId' => $this->lecturer_id,
            'Lecturer' => $this->whenLoaded('lecturer')->name,
        ];
    }
}
