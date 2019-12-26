<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{

    protected $fillable = ['school_program_id','user_id','home_university','guide_teacher_id','student_type',
        'current_postgraduate','type_income','is_available_final_work','is_ucv_teacher','repeat_approved_subject',
        'repeat_reprobated_subject','credits_granted','with_work','end_program','current_status','test_period'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function degrees()
    {
        return $this->hasMany('App\Degree');
    }

    public function guideTeacher()
    {
        return $this->hasOne('App\Teacher','id','guide_teacher_id')
            ->with('User');
    }

    public function equivalence()
    {
        return $this->hasMany('App\Equivalence');
    }

    public function schoolProgram()
    {
        return $this->belongsTo('App\SchoolProgram');
    }

    public static function addStudent($student)
    {
        try{
            return self::insertGetId($student);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function updateStudent($studentId,$student)
    {
        try{
            self::where('id',$studentId)
                ->get()[0]
                ->update($student);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function existStudentById($id,$organizationId)
    {
        try{
            return self::where('id',$id)
                ->whereHas('user',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getStudentById($id,$organizationId)
    {
        try{
            return self::where('id',$id)
                ->whereHas('user',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function studentHasProgram($userId)
    {
        try{
           return self::where('user_id',$userId)
               ->where('end_program',false)
               ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existStudentInProgram($userId,$programId)
    {
        try{
            return self::where('user_id',$userId)
                ->where('school_program_id',$programId)
                ->where('current_status','!=','RET-B')
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function activeStudentId($userId)
    {
        try{
            return self::where('user_id',$userId)
                ->where('end_program',false)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteStudent($userId,$studentId)
    {
        try{
            self::where('user_id',$userId)
                ->where('id',$studentId)
                ->delete();
        }catch (\Exception $e){
            return 0;
        }
    }
}
