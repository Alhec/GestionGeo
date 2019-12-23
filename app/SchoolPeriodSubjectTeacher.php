<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolPeriodSubjectTeacher extends Model
{
    protected $fillable = ['teacher_id','subject_id','school_period_id','limit','duty','enrolled_students','modality','start_date','end_date'];
    protected $table = 'school_period_subject_teacher';
    public $timestamps = false;

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

   public function teacher()
    {
        return $this->belongsTo('App\Teacher')->with('user');
    }

    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }

    public function schoolPeriod()
    {
        return $this->belongsTo('App\SchoolPeriod');
    }

    public static function addSchoolPeriodSubjectTeacher($schoolPeriodSubjectTeacher)
    {
        try{
            unset($schoolPeriodSubjectTeacher['schedules']);
            return self::insertGetId($schoolPeriodSubjectTeacher);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function  deleteSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId)
    {
        try{
            self::where('school_period_id',$schoolPeriodId)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }

    }

    public static function getSchoolPeriodSubjectTeacherBySchoolPeriod($schoolPeriodId)
    {
        try{
            return self::where('school_period_id',$schoolPeriodId)
                ->with('subject')
                ->with('teacher')
                ->with('schoolPeriod')
                ->with('schedules')
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateSchoolPeriodSubjectTeacher($id,$schoolPeriodSubjectTeacher)
    {
        try{
            self::find($id)
                ->update($schoolPeriodSubjectTeacher);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function findSchoolPeriodSubjectTeacherId($schoolPeriodId,$subjectId,$teacherId)
    {
        try{
            return self::where('school_period_id',$schoolPeriodId)
                ->where('subject_id',$subjectId)
                ->where('teacher_id',$teacherId)
                ->get('id');
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existSchoolPeriodSubjectTeacherBySchoolPeriodId($schoolPeriodId)
    {
        try{
            return self::where('school_period_id',$schoolPeriodId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteSchoolPeriodSubjectTeacher($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function updateEnrolledStudent($id)
    {
        try{
            $schoolPeriodSubjectTeacher = self::where('id',$id)
                ->get();
            if (is_numeric($schoolPeriodSubjectTeacher)&&$schoolPeriodSubjectTeacher==0){
                return 0;
            }
            $studentInSubject=StudentSubject::studentSubjectBySchoolPeriodSubjectTeacherId($id);
            if (is_numeric($studentInSubject)&&$studentInSubject==0){
                return 0;
            }
            $schoolPeriodSubjectTeacher->toArray()[0]['enrolled_students']= count($studentInSubject);
            $result = self::updateSchoolPeriodSubjectTeacher($id,$schoolPeriodSubjectTeacher->toArray()[0]);
            if (is_numeric($result)&& $result==0){
                return 0;
            }
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSchoolPeriodSubjectTeacherBySchoolPeriodTeacher($teacherId,$schoolPeriodId)
    {
        try{
            return self::where('school_period_id',$schoolPeriodId)
                ->where('teacher_id',$teacherId)
                ->with('subject')
                ->get();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function existSchoolPeriodSubjectTeacherById($id)
    {
        try{
            return self::where('id',$id)
                ->exists();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSchoolPeriodSubjectTeacherById($id)
    {
        try{
            return self::where('id',$id)
                ->get();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

}
