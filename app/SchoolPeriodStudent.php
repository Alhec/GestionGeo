<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriodStudent extends Model
{
    protected $fillable = ['student_id','school_period_id','pay_ref','status'];
    protected $table = 'school_period_student';
    public $timestamps = false;

    public function schoolPeriod()
    {
        return $this->belongsTo('App\SchoolPeriod');
    }
    public function student()
    {
        return $this->belongsTo('App\Student')->with('user');
    }

}
