<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CoursesPlans extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->resource->load('course');
        return [
            'Id' => $this->whenLoaded('course')->id,
            'MaMH'=>$this->whenLoaded('course')->code,
            'Name' => $this->whenLoaded('course')->name,
            'DVHT' => $this->whenLoaded('course')->dvht,
            'TongTiet' => $this->whenLoaded('course')->tong_tiet,
            'LT' => $this->whenLoaded('course')->lt,
            'BT' => $this->whenLoaded('course')->bt,
            'TH' => $this->whenLoaded('course')->th,
            'HK' => $this->whenLoaded('course')->hk,
            'GradeId'=>$this->whenLoaded('course')->grade_id,
        ];
    }
}
