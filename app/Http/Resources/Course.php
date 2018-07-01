<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Course extends JsonResource
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
            'Id' => $this->id,
            'MaMH'=>$this->ma_mh,
            'Name' => $this->name,
            'Code' => $this->code,
            'TongTiet' => $this->tong_tiet,
            'LT' => $this->lt,
            'BT' => $this->bt,
            'TH' => $this->th,
        ];
    }
}
