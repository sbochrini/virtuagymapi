<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps=false;
     protected $fillable = ['fisrtname', 'lastname', 'email', 'phone'];
    /*public function plans(){
        return $this->hasMany('App\Plan');
    }*/

    public function plans(){
        return $this->belongsToMany('App\Plan', 'user_plans', 'user_id', 'plan_id');;
    }
}
