<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = ['postgraduate_id','user_id','home_university','student_type','current_postgraduate','degrees'];

    public $timestamps = false;
    public function user() {
        return $this->belongsTo('App\User');
    }
}
