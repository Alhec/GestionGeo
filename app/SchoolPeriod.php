<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SchoolPeriod extends Model
{
    protected $fillable = ['cod_school_period','start_date','end_date','withdrawal_deadline','inscription_visible','organization_id','load_notes'];
    protected $hidden = ['organization_id'];
    public $timestamps = false;

    public function subjects()
    {
        return $this->hasMany('App\SchoolPeriodSubjectTeacher','school_period_id','id')
            ->with('subject')
            ->with('teacher')
            ->with('schedules');
    }

    public function inscriptions()
    {
        return $this->hasMany('App\SchoolPeriodStudent','school_period_id','id')
            ->with('enrolledSubjects');
    }
    public static function getSchoolPeriods($organizationId)
    {
        return self::where('organization_id',$organizationId)
            ->with('subjects')
            ->get();
    }

    public static function getSchoolPeriodById($id,$organizationId)
    {
        return self::where('id',$id)
            ->where('organization_id',$organizationId)
            ->with('subjects')
            ->get();
    }

    public static function getCurrentSchoolPeriod($organizationId)
    {
        return self::where('organization_id',$organizationId)
            ->whereDate('end_date','>=',date("Y-m-d"))
            ->whereDate('start_date','<=',date("Y-m-d"))
            ->orderBy('start_date','ASC')
            ->with('subjects')
            ->get();
    }

    public static function deleteSchoolPeriod($id)
    {
        self::find($id)
            ->delete();
    }

    public static function existSchoolPeriodById($id,$organizationId)
    {
        return self::where('id',$id)
            ->where('organization_id',$organizationId)
            ->exists();
    }

    public static function existSchoolPeriodByCodSchoolPeriod($codSchoolPeriod,$organizationId)
    {
        return self::where('cod_school_period',$codSchoolPeriod)
            ->where('organization_id',$organizationId)
            ->exists();
    }

    public static function addSchoolPeriod($schoolPeriod)
    {
        return self::insertGetId($schoolPeriod->only('cod_school_period','start_date','end_date','withdrawal_deadline','inscription_visible','organization_id','load_notes'));
    }

    public static function getSchoolPeriodByCodSchoolPeriod($codSchoolPeriod,$organizationId)
    {
        return self::where('cod_school_period',$codSchoolPeriod)
            ->where('organization_id',$organizationId)
            ->with('subjects')
            ->get();
    }

    public static function updateSchoolPeriod($id,$schoolPeriod)
    {
        self::find($id)
            ->update($schoolPeriod->all());
    }

    public static function getSubjectsByTeacher($teacherId)
    {
        return self::whereHas('subjects',function (Builder $query) use ($teacherId){
            $query
                ->where('teacher_id','=',$teacherId);
            })
            ->with('subjects')
            ->orderBy('start_date','ASC')
            ->get();
    }

    public static function getEnrolledSubjectsByStudent($studentId)
    {
        return self::whereHas('inscriptions',function (Builder $query) use ($studentId){
            $query
                ->where('student_id','=',$studentId);
            })
            ->with('inscriptions')
            ->orderBy('start_date','ASC')
            ->get();

    }
}
