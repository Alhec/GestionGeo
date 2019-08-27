<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriodSubjectTeacher extends Model
{
    protected $fillable = ['teacher_id','subject_id','school_period_id','limit','duty','enrolled_students'];
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
    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }
    public function schoolPeriod()
    {
        return $this->belongsTo('App\SchoolPeriod');
    }

    public static function addSchoolPeriodSubjectTeacher($schoolPeriodSubjectTeacher)
    {
        self::create($schoolPeriodSubjectTeacher);
    }

    public static function findSchoolPeriodSubjectTeacher($schoolPeriodId,$subjectId,$teacherId)
    {
        return self::where('school_period_id',$schoolPeriodId)
            ->where('subject_id',$subjectId)
            ->where('teacher_id',$teacherId)
            ->get();
    }
}
