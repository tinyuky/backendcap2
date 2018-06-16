<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';
    protected $fillable = [
        'name','class_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
    
    public function students(){
        return hasMany('App\Students','class_id','id');
    }
    
    public function grade(){
        return belongsTo('App\Grades');
    }
}
