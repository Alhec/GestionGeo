<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriodSubjectTeacher extends Model
{
    protected $table = 'school_period_subject_teacher';
    public $timestamps = false;

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }
    public function teacher()
    {
        return $this->belongsTo('App\Teacher')->with('user');
    }
    public function schedule()
    {
        return $this->hasMany('App\Schedule');
    }
}
