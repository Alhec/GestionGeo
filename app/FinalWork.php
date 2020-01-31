<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinalWork extends Model
{
    public $timestamps = false;
    protected $table = 'final_works';
    protected $fillable = ['id','title','student_id','subject_id','project_id','is_project?','approval_date'];

    public function SchoolPeriods()
    {
        return $this->belongsToMany('App\SchoolPeriod','final_work_school_period')
            ->as('finalWorkSchoolPeriod')
            ->withPivot('status','description_status','final_work_id','school_period_id');
    }

    public function Advisors()
    {
        return $this->belongsToMany('App\Teachers','advisors')
            ->as('advisors')
            ->withPivot('teacher_id','final_work_id');
    }

    public static function existApprovedFinalWork($studentId, $isProject)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project?',$isProject)
                ->where('status','APR')
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getFinalWorkByStudentAndStatus($studentId, $isProject, $status)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project?',$isProject)
                ->where('status',$status)
                ->with('SchoolPeriods')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getFinalWorksByStudent($studentId, $isProject)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project?',$isProject)
                ->with('SchoolPeriods')
                ->get('id');
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function addFinalWork($finalWork)
    {
        try{
            return self::insertGetId($finalWork);
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateFinalWork($id,$finalWork)
    {
        try{
            return self::find($id)
                ->update($finalWork);
        }catch (\Exception $e){
            return 0;
        }
    }
}
