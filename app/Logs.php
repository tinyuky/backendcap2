<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'logs';
    protected $fillable = [
        'name','option','user_id'
    ];
    protected $hidden = [
        'created_at', 'updated_at',
    ];
    public function students(){
        return $this->hasMany('App\Students','log_id','id');
    }
}
