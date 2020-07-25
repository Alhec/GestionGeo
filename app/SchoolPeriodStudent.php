<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SchoolPeriodStudent extends Model
{
    protected $fillable = ['student_id','school_period_id','status','financing','financing_description','pay_ref',
        'amount_paid','inscription_date','test_period'];

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
        return $this->hasMany('App\StudentSubject')
            ->with('dataSubject');
    }

    public function finalWorkData()
    {
        return $this->hasMany('App\FinalWorkSchoolPeriod')
            ->with('finalWork');
    }

    public function doctoralExam()
    {
        return $this->hasOne('App\DoctoralExam','school_period_student_id','id');
    }

    public static function getSchoolPeriodStudent($organizationId)
    {
        try{
            return self::with('schoolPeriod')
                ->with('student')
               /* ->with('enrolledSubjects')
                ->with('finalWorkData')*/
                ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSchoolPeriodStudentById($id,$organizationId)
    {
        try{
            return self::where('id',$id)
                ->with('student')
                ->with('enrolledSubjects')
                ->with('schoolPeriod')
                ->with('finalWorkData')
                ->with('doctoralExam')
                ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSchoolPeriodStudentBySchoolPeriod($schoolPeriodId,$organizationId)
    {
        try{
            return self::where('school_period_id',$schoolPeriodId)
                ->with('student')
                ->with('enrolledSubjects')
                ->with('schoolPeriod')
                ->with('finalWorkData')
                ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existSchoolPeriodStudent($studentId,$schoolPeriodId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('school_period_id',$schoolPeriodId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function addSchoolPeriodStudent($schoolPeriodStudent){
        try{
            return self::insertGetId($schoolPeriodStudent->only('student_id','school_period_id','status','financing',
                'financing_description','pay_ref','amount_paid','test_period'));
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function addSchoolPeriodStudentLikeArray($schoolPeriodStudent){
        try{
            return self::create($schoolPeriodStudent);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function existSchoolPeriodStudentById($id,$organizationId){
        try{
            return self::where('id',$id)
                ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteSchoolPeriodStudent($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function findSchoolPeriodStudent($studentId,$schoolPeriodId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('school_period_id',$schoolPeriodId)
                ->with('enrolledSubjects')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateSchoolPeriodStudent($id,$schoolPeriodSubject)
    {
        try{
            self::find($id)
                ->update($schoolPeriodSubject->all());
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function updateSchoolPeriodStudentLikeArray($id,$schoolPeriodSubject)
    {
        try{
            self::find($id)
                ->update($schoolPeriodSubject);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function isThereUnpaidSchoolPeriod($studentId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('amount_paid',null)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getEnrolledSchoolPeriodsByStudent($studentId, $organizationId)
    {
        try{
            return self::where('student_id',$studentId)
                ->with('student')
                ->with('enrolledSubjects')
                ->with('schoolPeriod')
                ->with('finalWorkData')
                ->whereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->orderBy('inscription_date','ASC')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getCantEnrolledSchoolPeriodByStudent($studentId,$organizationId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where(function ($query){
                    $query->where('status','REG')
                        ->orWhere('status','RIN-A')
                        ->orWhere('status','RIN-B')
                        ->orWhere('status','REI-A')
                        ->orWhere('status','REI-B');
                })
                ->WhereHas('schoolPeriod',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->count();
        }catch (\Exception $e){
            return 'e';
        }
    }


}
