<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SchoolProgramSubject extends Model
{
    protected $table = 'school_program_subject';

    protected $fillable = ['school_program_id','subject_id','type','subject_group'];

    public $timestamps = false;

    public static function addSchoolProgramSubject($schoolProgramSubject)
    {
        try{
            return self::insertGetId($schoolProgramSubject);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSchoolProgramSubjectBySubjectId($subjectId)
    {
        try{
            return self::where('subject_id',$subjectId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function updateSchoolProgramSubject($id, $schoolProgramSubject)
    {
        try{
            self::find($id)
                ->update($schoolProgramSubject);
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function deleteSchoolProgramSubject($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSchoolProgramSubjectBySubjectSchoolPogram($subjectId,$schoolProgramId)
    {
        try{
            self::where('subject_id',$subjectId)
                ->where('school_program_id',$schoolProgramId)
                ->get('subject_together_id');
        }catch (\Exception $e){
            return 0;
        }
    }
}
