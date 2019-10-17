<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolProgramSubject extends Model
{
    protected $table = 'school_program_subject';

    protected $fillable = ['school_program_id','subject_id','type'];

    public $timestamps = false;

    public static function addSchoolProgramSubject($schoolProgramSubject)
    {
        return self::insertGetId($schoolProgramSubject);
    }

    public static function getSchoolProgramSubjectBySubjectId($subjectId)
    {
        return self::where('subject_id',$subjectId)
            ->get();
    }

    public static function updateSchoolProgramSubject($id, $schoolProgramSubject)
    {
        self::find($id)
            ->update($schoolProgramSubject);
    }

    public static function deleteSchoolProgramSubject($id)
    {
        self::find($id)
            ->delete();
    }
}
