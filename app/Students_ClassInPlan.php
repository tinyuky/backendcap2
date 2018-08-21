<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Students_ClassInPlan extends Model
{
    protected $table = 'student_classinplan';
    protected $fillable = [
        'id','stundent_id','classinplan_id'
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
