<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriod extends Model
{
    protected $fillable = ['cod_school_period','start_date','end_date','duty','inscription_visible','end_school_period'];
    public $timestamps = false;

    public function subject()
    {
        return $this->hasMany('App\SchoolPeriodSubjectTeacher','school_period_id','id')->with('subject')->with('teacher')->with('schedule');
    }
}
