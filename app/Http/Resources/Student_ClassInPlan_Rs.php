<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Classes_CoursesPlan;
use App\Http\Resources\Classes_CoursesPlanRs as Classes_CoursesPlanResource;

class Student_ClassInPlan_Rs extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return new Classes_CoursesPlanResource(Classes_CoursesPlan::find($this->classinplan_id));
    }
}
