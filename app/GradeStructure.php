<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradeStructure extends Model
{
    protected $table = 'gradestructure';
    protected $fillable = [
        'name','classinplan_id','sheet'
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
