<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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


}
