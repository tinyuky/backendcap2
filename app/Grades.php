<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grades extends Model
{
    protected $table = 'grades';
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
    
    public function classes()
    {
        return $this->hasMany('App\Classes','grade_id','id');
    }
    
    public function students(){
        return hasMany('App\Students','grade_id','id');
    }

    public function plans(){
        return hasMany('App\Grades_Plans','grade_id','id');
    }

    public function courses(){
        return hasMany('App\Courses','grade_id','id');
    }

}
