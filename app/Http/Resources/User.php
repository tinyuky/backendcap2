<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'StaffId'=>$this->staffid,
            'Name' => $this->name,
            'Email' => $this->email,
            'OtherEmail' => $this->otheremail,
            'Password' => $this->password,
            'Phone1' => $this->phone1,
            'Phone2' => $this->phone2,
            'Role' => $this->role,
            'Status' => $this->status,
        ];
    }
}
