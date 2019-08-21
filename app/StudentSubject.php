<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    protected $fillable = ['student_id','school_period_subject_teacher_id','qualification','status'];

    protected $table = 'student_subject';
    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo('App\Student')->with('user');
    }

    public function subject(){
        return $this->belongsTo('App\SchoolPeriodSubjectTeacher','school_period_subject_teacher_id','id')->with('teacher')->with('subject')->with('schoolPeriod')->with('schedule');
    }
}
