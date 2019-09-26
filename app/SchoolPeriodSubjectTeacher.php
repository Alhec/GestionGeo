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
        unset($schoolPeriodSubjectTeacher['schedules']);
        return self::insertGetId($schoolPeriodSubjectTeacher);
    }

    public static function  deleteSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId)
    {
        self::where('school_period_id',$schoolPeriodId)
            ->delete();
    }

    public static function getSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId)
    {
        return self::where('school_period_id',$schoolPeriodId)
            ->with('subject')
            ->with('teacher')
            ->with('schoolPeriod')
            ->get();
    }

    public static function updateSchoolPeriodSubjectTeacher($id,$schoolPeriodSubjectTeacher)
    {
        self::find($id)
            ->update($schoolPeriodSubjectTeacher);
    }

    public static function findSchoolPeriodSubjectTeacherId($schoolPeriodId,$subjectId,$teacherId)
    {
        return self::where('school_period_id',$schoolPeriodId)
            ->where('subject_id',$subjectId)
            ->where('teacher_id',$teacherId)
            ->get('id');
    }

    public static function existSchoolPeriodSubjectTeacherBySchoolPeriodId($schoolPeriodId)
    {
        return self::where('school_period_id',$schoolPeriodId)
            ->exists();
    }

    public static function deleteSchoolPeriodSubjectTeacher($id)
    {
        self::find($id)
            ->delete();
    }

    public static function updateEnrolledStudent($id)
    {
        $schoolPeriodSubjectTeacher = self::where('id',$id)
            ->get()->toArray();
        $schoolPeriodSubjectTeacher[0]['enrolled_students']= count(StudentSubject::studentSubjectBySchoolPeriodSubjectTeacherId($id));
        self::updateSchoolPeriodSubjectTeacher($id,$schoolPeriodSubjectTeacher[0]);
    }
}
