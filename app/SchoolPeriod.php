<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SchoolPeriod extends Model
{
    protected $fillable = ['organization_id','cod_school_period','start_date','end_date','withdrawal_deadline',
        'load_notes','inscription_start_date','inscription_visible'];
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
        try{
            return self::where('organization_id',$organizationId)
                ->with('subjects')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSchoolPeriodById($id,$organizationId)
    {
        try{
            return self::where('id',$id)
                ->where('organization_id',$organizationId)
                ->with('subjects')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getCurrentSchoolPeriod($organizationId)
    {
        try{
            return self::where('organization_id',$organizationId)
                ->whereDate('end_date','>=',date("Y-m-d"))
                ->whereDate('start_date','<=',date("Y-m-d"))
                ->orderBy('start_date','ASC')
                ->with('subjects')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteSchoolPeriod($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function existSchoolPeriodById($id,$organizationId)
    {
        try{
            return self::where('id',$id)
                ->where('organization_id',$organizationId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existSchoolPeriodByCodSchoolPeriod($codSchoolPeriod,$organizationId)
    {
        try{
            return self::where('cod_school_period',$codSchoolPeriod)
                ->where('organization_id',$organizationId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }

    }

    public static function addSchoolPeriod($schoolPeriod)
    {

        try{
            return self::insertGetId($schoolPeriod->only('organization_id','cod_school_period','start_date','end_date',
                'withdrawal_deadline','load_notes','inscription_start_date','inscription_visible'));
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSchoolPeriodByCodSchoolPeriod($codSchoolPeriod,$organizationId)
    {
        try{
            return self::where('cod_school_period',$codSchoolPeriod)
                ->where('organization_id',$organizationId)
                ->with('subjects')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateSchoolPeriod($id,$schoolPeriod)
    {
        try{
            self::find($id)
                ->update($schoolPeriod->all());
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSubjectsByTeacher($teacherId)
    {
        try{
            return self::whereHas('subjects',function (Builder $query) use ($teacherId){
                $query
                    ->where('teacher_id','=',$teacherId);
                })
                ->with('subjects')
                ->orderBy('start_date','ASC')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }
}
