<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\StudentSubject;

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

    public static function findSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId)
    {
        return self::where('school_period_id',$schoolPeriodId)
            ->get();
    }

    public static function existSchoolPeriodSubjectTeacherById($id)
    {
        return self::where('id',$id)
            ->exists();
    }

    public static function  deleteSchoolPeriodSubjectTeacher($id)
    {
        self::find($id)
        ->delete();
    }

    public static function  deleteSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId)
    {
        self::where('school_period_id',$schoolPeriodId)
            ->delete();
    }

    public static function updateSchoolPeriodSubjectTeacher($id,$schoolPeriodSubjectTeacher)
    {
        self::find($id)
            ->update($schoolPeriodSubjectTeacher);
    }

    public static function updateEnrolledStudent($id)
    {
        $schoolPeriodSubjectTeacher = self::where('id',$id)
        ->get();
        $schoolPeriodSubjectTeacher[0]['enrolled_students']= count(StudentSubject::studentSubjectBySchoolPeriodSubjectTeacherId($id));
        self::find($id)
        ->update($schoolPeriodSubjectTeacher->all());
    }
     public static function getSchoolPeriodSubjectTeacher($id)
     {
         return self::where('id',$id)
             ->get();
     }
}
