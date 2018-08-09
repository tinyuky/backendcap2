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
            'Id' => $this->id,
            'MaMH'=>$this->whenLoaded('course')->code,
            'Name' => $this->whenLoaded('course')->name,
            'DVHT' => $this->dvht,
            'TongTiet' => $this->tong_tiet,
            'LT' => $this->lt,
            'BT' => $this->bt,
            'TH' => $this->th,
            'DA' => $this->da,
            'TC' => $this->tc,
            'SG' => $this->sg,
            'Grade'=> $this->whenLoaded('course')->grade->name,
        ];
    }
}
