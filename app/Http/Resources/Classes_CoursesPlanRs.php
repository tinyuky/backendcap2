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
        return [
            'Id'=> $this->id,
            'Name' => $this->name,
            'CoursePlanId' => $this->courseplan_id,
            'LecturerId' => $this->lecturer_id
        ];
    }
}
