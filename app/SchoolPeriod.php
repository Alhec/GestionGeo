<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriod extends Model
{
    protected $fillable = ['cod_school_period','start_date','end_date','inscription_visible','organization_id','load_notes'];
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

    public static function existSchoolPeriod($codSchoolPeriod,$organizationId)
    {
        return self::where('cod_school_period',$codSchoolPeriod)
            ->where('organization_id',$organizationId)
            ->exists();
    }

    public static function addSchoolPeriod($schoolPeriod)
    {
        self::create($schoolPeriod->all());
    }

    public static function findSchoolPeriodId($codSchoolPeriod,$organizationId)
    {
        $schoolPeriod = self::where('cod_school_period',$codSchoolPeriod)
            ->where('organization_id',$organizationId);
        if ($schoolPeriod->exists()){
            return $schoolPeriod->get()[0];
        }
        return null;
    }

    public static function existSchoolPeriodById($id,$organizationId)
    {
        return self::where('id',$id)
            ->where('organization_id',$organizationId)
            ->exists();
    }

    public static function deleteSchoolPeriod($id)
    {
        self::find($id)
            ->delete();
    }

    public static function updateSchoolPeriod($id,$schoolPeriod)
    {
        self::find($id)
            ->update($schoolPeriod->all());
    }

}
