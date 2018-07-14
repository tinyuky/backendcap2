<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grades_Plans extends Model
{
    protected $table = 'grades_plans';
    protected $fillable = [
        'id','name','grade_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
    
    public function courses(){
        return $this->hasMany('App\Course_Plans','plan_id','id');
    }
}
