<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    protected $table = 'courses';
    protected $fillable = [
        'id','name','code','dvht','tong_tiet','lt','th','bt','hk'
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
        return belongsTo('App\Grades');
    }

    public function grades_plans(){
        return belongsToMany('App\Grades_Plans');
    }
}
