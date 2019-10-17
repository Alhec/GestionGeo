<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subject extends Model
{
    protected $fillable = ['subject_code','subject_name','uc','subject_type','is_final_subject?'];

    public $timestamps = false;

    public function SchoolPrograms()
    {
        return $this->belongsToMany('App\SchoolProgram','school_program_subject')
            ->as('schoolProgramSubject')
            ->withPivot('type');
    }

    public static function getSubjects($organizationId){
        return self::with('schoolPrograms')
            ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();
    }

    public static function getSubjectById($id,$organizationId){
        return self::where('id',$id)
            ->with('schoolPrograms')
            ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();
    }

    public static function existSubjectByCode($code,$organizationId){
        return self::where('subject_code',$code)
            ->with('schoolPrograms')
            ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->exists();
    }

    public static function addSubject($subject)
    {
        return self::insertGetId($subject->only('subject_code','subject_name','uc','subject_type','is_final_subject?'));
    }

    public static function getSubjectByCode($code,$organizationId)
    {
        return self::where('subject_code',$code)
            ->with('schoolPrograms')
            ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->get();
    }

    public static function existSubjectById($id,$organizationId)
    {
        return self::where('id',$id)
            ->with('schoolPrograms')
            ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                $query
                    ->where('organization_id','=',$organizationId);
            })
            ->exists();
    }

    public static function deleteSubject($id)
    {
        self::find($id)
            ->delete();
    }

    public static function updateSubject($id,$subject)
    {
        self::find($id)
            ->update($subject->all());
    }

    public static function getSubjectsBySchoolProgram($schoolProgramId, $organizationId){
        return self::whereHas('schoolPrograms',function (Builder $query) use ($schoolProgramId,$organizationId){
                $query
                    ->where('organization_id','=',$organizationId)
                    ->where('school_program_id','=',$schoolProgramId);
            })
            ->get('id');
    }
}
