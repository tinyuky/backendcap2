<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'log';
    protected $fillable = [
        'student_id','action','status','user_id'
    ];
    protected $hidden = [
        'created_at', 'updated_at',
    ];
    public $timestamps = false;
}
