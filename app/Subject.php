<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    protected $fillable = ['subject_code','subject_name','uc','is_final_subject?','theoretical_hours','practical_hours','laboratory_hours'];

    public $timestamps = false;

    public function SchoolPrograms()
    {
        return $this->belongsToMany('App\SchoolProgram','school_program_subject')
            ->as('schoolProgramSubject')
            ->withPivot('type');
    }

   /* public function relationsSubjects()
    {
        return $this->hasMany('App\SchoolPeriodSubjectTeacher','subject_id','id')
            ->with('schoolPeriod');
    }*/

    public static function getSubjects($organizationId){
        try{
            return self::with('schoolPrograms')
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSubjectById($id,$organizationId){
        try{
            return self::where('id',$id)
                ->with('schoolPrograms')
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existSubjectByCode($code,$organizationId){
        try{
            return self::where('subject_code',$code)
                ->with('schoolPrograms')
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->exists();
        }catch (\Exception $e){
            return 0;
        }

    }

    public static function addSubject($subject)
    {
        try{
            return self::insertGetId($subject->only('subject_code','subject_name','uc','is_final_subject?','theoretical_hours','practical_hours','laboratory_hours'));
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSubjectByCode($code,$organizationId)
    {
        try{
            return self::where('subject_code',$code)
                ->with('schoolPrograms')
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }

    }

    public static function existSubjectById($id,$organizationId)
    {
        try{
            return self::where('id',$id)
                ->with('schoolPrograms')
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function deleteSubject($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function updateSubject($id,$subject)
    {
        try{
            self::find($id)
                ->update($subject->all());
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSubjectsBySchoolProgram($schoolProgramId, $organizationId){
        try{
            return self::whereHas('schoolPrograms',function (Builder $query) use ($schoolProgramId,$organizationId){
                $query
                    ->where('organization_id','=',$organizationId)
                    ->where('school_program_id','=',$schoolProgramId);
            })
                ->get('id');
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

   /* public static function getSubjectsByTeacher($teacherId){
        try{
            return self::whereHas('relationsSubjects',function (Builder $query) use ($teacherId){
                $query
                    ->where('teacher_id','=',$teacherId);
            })
                ->with('relationsSubjects')
                ->get();
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }*/
}
