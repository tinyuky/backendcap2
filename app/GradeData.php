<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradeData extends Model
{
    protected $table = 'gradedata';
    protected $fillable = [
        'name','gradestructure_id','student_id','grade'
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
