<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classes_CoursesPlan extends Model
{
    protected $table = "classes_plans";
    protected $fillable = [
        'name','courseplan_id','lecturer_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function courseinplan(){
        return $this->belongsTo('App\Course_Plans','courseplan_id');
    }
    
    public function lecturer(){
        return $this->belongsTo('App\User','lecturer_id');
    }
}
