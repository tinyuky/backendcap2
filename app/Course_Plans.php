<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course_Plans extends Model
{
    protected $table = 'courses_plan';
    protected $fillable = [
        'id','course_id','plan_id','hk','dvht','tong_tiet','lt','th','bt','da'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function course(){
        return $this->belongsTo('App\Courses','course_id');
    }

    public function plan(){
        return $this->belongsTo('App\Grades_Plans','plan_id');
    }
}
