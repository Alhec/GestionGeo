<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FinalWork extends Model
{
    public $timestamps = false;
    protected $table = 'final_works';
    protected $fillable = ['title','student_id','subject_id','project_id','is_project','approval_date'];

    public function schoolPeriods()
    {
        return $this->belongsToMany('App\SchoolPeriodStudent','final_work_school_period')
            ->as('finalWorkSchoolPeriod')
            ->withPivot('id','status','description_status','final_work_id','school_period_student_id');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Teacher','advisors')
            ->as('advisors')
            ->withPivot('teacher_id','final_work_id')
            ->with('user');
    }

    public static function existNotApprovedFinalWork($studentId, $isProject)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project',$isProject)
                ->where('approval_date','=',null)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getNotApprovedFinalWork($studentId, $isProject)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project',$isProject)
                ->where('approval_date',null)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }
    public static function getFinalWorkByStudentAndStatus($studentId, $isProject, $status)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project',$isProject)
                ->whereHas('schoolPeriods',function (Builder $query) use ($status) {
                    $query
                        ->where('final_work_school_period.status','=',$status);
                })
                ->with('teachers')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getFinalWorksByStudent($studentId, $isProject)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project',$isProject)
                ->with('schoolPeriods')
                ->with('teachers')
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

    public static function getFinalWorksByStudentSubject($studentId,$subjectId, $isProject)
    {
        try{
            return self::where('student_id',$studentId)
                ->where('is_project',$isProject)
                ->where('subject_id',$subjectId)
                ->with('schoolPeriods')
                ->with('teachers')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existFinalWorkByIdAndStatus($id, $isProject, $status)
    {
        try{
            return self::where('id',$id)
                ->where('is_project',$isProject)
                ->whereHas('schoolPeriods',function (Builder $query) use ($status) {
                    $query
                        ->where('final_work_school_period.status','=',$status);
                })
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteFinalWork($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getFinalWork($id)
    {
        try{
            return self::where('id',$id)
                ->with('schoolPeriods')
                ->with('teachers')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existFinalWorkById($id)
    {
        try{
            return self::where('id',$id)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

}
