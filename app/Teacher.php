<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id','teacher_type'];
    public $timestamps = false;
    public function user() {
        return $this->belongsTo('App\User');
    }
}
