<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    protected $fillable = ['school_period_student_id','school_period_subject_teacher_id','qualification','status'];

    protected $table = 'student_subject';
    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo('App\SchoolPeriodStudent');
    }

    public function subject()
    {
        return $this->belongsTo('App\SchoolPeriodSubjectTeacher','school_period_subject_teacher_id','id')
            ->with('subject')
            ->with('schedules');
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

    public static function existStudentSubject($id)
    {
        self::where('id',$id)
            ->exists();
    }

    public static function deleteStudentSubject($id)
    {
        self::find($id)
            ->delete();
    }

    public static function findStudentSubjectBySchoolPeriodStudent($schoolPeriodStudentId)
    {
        return self::where('school_period_student_id',$schoolPeriodStudentId)
            ->get();
    }

    public static function updateStudentSubject($id,$studentSubject)
    {
        self::find($id)
            ->update($studentSubject);
    }
    public  static function findSchoolPeriodStudent($schoolPeriodStudentId,$schoolPeriodSubjectTeacherId)
    {
        return self::where('school_period_student_id',$schoolPeriodStudentId)
            ->where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacherId)
            ->get();
    }
}
