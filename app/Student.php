<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{

    protected $fillable = ['school_program_id','user_id','guide_teacher_id','student_type','home_university',
        'current_postgraduate','type_income','is_ucv_teacher','is_available_final_work','repeat_approved_subject',
        'repeat_reprobated_subject','credits_granted','with_work','end_program','test_period','current_status'];

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
        return $this->belongsTo('App\Teacher','id','guide_teacher_id')
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

    public function schoolPeriod()
    {
        return $this->belongsTo('App\SchoolPeriodStudent');
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
                ->with('user')
                ->with('degrees')
                ->with('guideTeacher')
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

    public static function warningStudent($organizationId)
    {
        try{
            return self::whereHas('user',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->where('current_status','DES-A')
                ->orWhere('current_status','DES-B')
                ->orWhere('current_status','RET-A')
                ->orWhere('current_status','RET-B')
                ->orWhere('test_period',true)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getStudentActive($organizationId)
    {
        try{
            return self::whereHas('user',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId)
                    ->where('active','=',true);
                })
                ->where('end_program',false)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }
}
