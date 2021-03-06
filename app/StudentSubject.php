<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StudentSubject extends Model
{
    protected $fillable = ['school_period_student_id','school_period_subject_teacher_id','qualification','status'];

    protected $table = 'student_subject';

    public $timestamps = false;

    public function dataStudent()
    {
        return $this->belongsTo('App\SchoolPeriodStudent','school_period_student_id','id')
            ->with('student');
    }

    public function dataSubject()
    {
        return $this->belongsTo('App\SchoolPeriodSubjectTeacher','school_period_subject_teacher_id','id')
            ->with('subject')
            ->with('schedules')
            ->with('teacher');
    }

    public static function getAllSubjectsEnrolledWithoutRET($studentId)
    {
        try{
            return self::where('status','!=','RET')
                ->with('dataSubject')
                ->whereHas('dataStudent',function (Builder $query) use ($studentId){
                    $query
                        ->where('student_id','=',$studentId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getAllSubjectsEnrolledWithoutRETCUR($studentId)
    {
        try{
            return self::where('status','!=','RET')
                ->where('status','!=','CUR')
                ->with('dataSubject')
                ->whereHas('dataStudent',function (Builder $query) use ($studentId){
                    $query
                        ->where('student_id','=',$studentId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function cantAllSubjectsEnrolledWithoutRETCUR($studentId)
    {
        try{
            return self::where('status','!=','RET')
                ->where('status','!=','CUR')
                ->whereHas('dataStudent',function (Builder $query) use ($studentId){
                    $query
                        ->where('student_id','=',$studentId);
                })
                ->count();
        }catch (\Exception $e){
            return 'e';//retorna e porque esta ocasion en un caso correcto puede devolver 0
        }
    }

    public static function getEnrolledSubjectsBySchoolPeriodStudent($studentId,$schoolPeriodId)
    {
        try{
            return self::with('dataSubject')
                ->whereHas('dataStudent',function (Builder $query) use ($studentId,$schoolPeriodId){
                    $query
                        ->where('student_id','=',$studentId)
                        ->where('school_period_id','=',$schoolPeriodId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function addStudentSubject($studentSubject)
    {
        try{
            self::create($studentSubject);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function studentSubjectBySchoolPeriodSubjectTeacherId($schoolPeriodSubjectTeacherId)
    {
        try{
            return self::where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacherId)
                ->with('dataStudent')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function studentSubjectBySchoolPeriodStudent($schoolPeriodStudentId)
    {
        try{
            return self::where('school_period_student_id',$schoolPeriodStudentId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public  static function findStudentSubjectId($schoolPeriodStudentId, $schoolPeriodSubjectTeacherId)
    {
        try{
            return self::where('school_period_student_id',$schoolPeriodStudentId)
                ->where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacherId)
                ->get('id');
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateStudentSubject($id,$studentSubject)
    {
        try{
            self::find($id)
                ->update($studentSubject);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteStudentSubject($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteStudentSubjectBySchoolPeriodStudentId($schoolPeriodStudentId)
    {
        try{
            self::where('school_period_student_id',$schoolPeriodStudentId)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getStudentSubjectById($id)
    {
        try{
            return self::where('id',$id)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }
}
