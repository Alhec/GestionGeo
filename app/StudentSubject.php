<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StudentSubject extends Model
{
    protected $fillable = ['school_period_student_id','school_period_subject_teacher_id','qualification','status'];

    protected $table = 'student_subject';
    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo('App\SchoolPeriodStudent','school_period_student_id','id');
    }

    public function dataSubject()
    {
        return $this->belongsTo('App\SchoolPeriodSubjectTeacher','school_period_subject_teacher_id','id')
            ->with('subject')
            ->with('schedules');
    }

    public static function getApprovedSubjects($studentId)
    {
        return self::where('status','APR')
            ->with('dataSubject')
            ->whereHas('student',function (Builder $query) use ($studentId){
                $query
                    ->where('student_id','=',$studentId);
            })
            ->get();
    }

    public static function getEnrolledSubjectsBySchoolPeriod($studentId,$schoolPeriodId)
    {
        return self::with('dataSubject')
            ->whereHas('student',function (Builder $query) use ($studentId,$schoolPeriodId){
                $query
                    ->where('student_id','=',$studentId)
                    ->where('school_period_id','=',$schoolPeriodId);
            })
            ->get();
    }

    public static function addStudentSubject($studentSubject)
    {
        self::create($studentSubject);
    }

    public static function studentSubjectBySchoolPeriodSubjectTeacherId($schoolPeriodSubjectTeacherId)
    {
        return self::where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacherId)
            ->get();
    }

    public static function studentSubjectBySchoolPeriodStudent($schoolPeriodStudentId)
    {
        return self::where('school_period_student_id',$schoolPeriodStudentId)
            ->get();
    }

    public  static function findSchoolPeriodStudentId($schoolPeriodStudentId,$schoolPeriodSubjectTeacherId)
    {
        return self::where('school_period_student_id',$schoolPeriodStudentId)
            ->where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacherId)
            ->get('id');
    }

    public static function updateStudentSubject($id,$studentSubject)
    {
        self::find($id)
            ->update($studentSubject);
    }

    public static function deleteStudentSubject($id)
    {
        self::find($id)
            ->delete();
    }

}
