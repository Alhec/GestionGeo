<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        return $this->belongsTo('App\Student')
            ->with('user');
    }
    public function enrolledSubjects()
    {
        return $this->hasMany('App\StudentSubject')->with('subject');
    }
    public static function getSchoolPeriodStudent($organizationId)
    {
        return self::with('schoolPeriod')
            ->with('student')
            ->with('enrolledSubjects')
            ->with('schoolPeriod')
            ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();
    }
    public static function getSchoolPeriodStudentById($id,$organizationId)
    {
        return self::where('id',$id)
            ->with('student')
            ->with('enrolledSubjects')
            ->with('schoolPeriod')
            ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();
    }
    public static function addSchoolPeriodStudent($schoolPeriodStudent){
        self::create($schoolPeriodStudent->all());
    }

    public static function existSchoolPeriodStudent($studentId,$schoolPeriodId)
    {
        return self::where('student_id',$studentId)
            ->where('school_period_id',$schoolPeriodId)
            ->exists();
    }
    public static function findSchoolPeriodStudent($studentId,$schoolPeriodId)
    {
        return self::where('student_id',$studentId)
            ->where('school_period_id',$schoolPeriodId)
            ->get();
    }
    public static function existSchoolPeriodStudentById($id,$organizationId){
        return self::where('id',$id)
            ->with('student')
            ->with('enrolledSubjects')
            ->with('schoolPeriod')
            ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->exists();
    }

    public static function deleteSchoolPeriodStudent($id)
    {
        self::find($id)
            ->delete();
    }

    public static function updateSchoolPeriodStudent($id,$schoolPeriodSubject)
    {
        self::find($id)
            ->update($schoolPeriodSubject->all());
    }


}
