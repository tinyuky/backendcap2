<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    protected $table = 'students';
    protected $fillable = [
        'student_id','name','gender','dob','pob','code1','code2','note','status','class_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
    
    public function grade(){
        return $this->belongsTo('App\Grades','grade_id');
    }
    
    public function class(){
        return $this->belongsTo('App\Classes','class_id');
    }
}
