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
}
