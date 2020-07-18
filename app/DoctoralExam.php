<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DoctoralExam extends Model
{
    protected $fillable = ['school_period_student_id','status'];
    public $timestamps = ["created_at"]; //only want to used created_at column
    const UPDATED_AT = null; //and updated by default null set

    public function inscription()
    {
        return $this->belongsTo('App\SchoolPeriodStudent','school_period_student_id','id');
    }

    public static function addDoctoralExam($schoolPeriodStudentId,$status)
    {
        try{
             self::create([
                'school_period_student_id'=>$schoolPeriodStudentId,
                'status'=>$status
            ]);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteDoctoralExam($schoolPeriodStudentId)
    {
        try{
            self::where('school_period_student_id',$schoolPeriodStudentId)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function existDoctoralExamApprovedByStudent($studentId)
    {
         try{
            return self::where('status','APPROVED')
                ->whereHas('inscription',function (Builder $query) use ($studentId){
                $query
                    ->where('student_id','=',$studentId);
                })
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getDoctoralExamByStudent($studentId)
    {
        try{
            return self::whereHas('inscription',function (Builder $query) use ($studentId){
                $query
                    ->where('student_id','=',$studentId);
            })->get();
        }catch (\Exception $e){
            return 0;
        }
    }
}
