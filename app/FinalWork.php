<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinalWork extends Model
{
    public $timestamps = false;
    protected $table = 'final_works';
    protected $fillable = ['id','title','student_id','subject_id','project_id','is_project?','status',
        'description_status','approval_date'];

    public function SchoolPeriods()
    {
        return $this->belongsToMany('App\SchoolPeriod','final_work_school_period')
            ->as('finalWorkSchoolPeriod')
            ->withPivot('status','description_status','final_work_id','school_period_id');
    }

    public static function existApprovedProject($studentId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project?',true)
                ->where('status','APR')
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getProjectInProgressByStudent($studentId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project?',true)
                ->where('status','progress')
                ->with('SchoolPeriods')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getProjectsByStudent($studentId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project?',true)
                ->with('SchoolPeriods')
                ->get('id');
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getTesisByStudent($studentId)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project?',false)
                ->get();
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
