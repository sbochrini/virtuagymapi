<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseInstance extends Model
{
    protected $table = 'exercise_instances';
    public $timestamps = false;

    public function exerciseName(){
      return $this->hasOne('App\Exercise', 'id', 'exercise_id');
    }
}
