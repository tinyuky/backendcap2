<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course_Plans extends Model
{
    protected $table = 'courses_plan';
    protected $fillable = [
        'id','course_id','plan_id','hk'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    

}
