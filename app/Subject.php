<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    protected $fillable = ['code','name','uc','is_final_subject','is_project_subject','theoretical_hours',
        'practical_hours','laboratory_hours'];

    public $timestamps = false;

    public function schoolPrograms()
    {
        return $this->belongsToMany('App\SchoolProgram','school_program_subject')
            ->as('schoolProgramSubject')
            ->withPivot('type','subject_group');
    }

    public static function getSubjects($organizationId, $perPage=0){
        try{
            if ($perPage == 0){
                return self::with('schoolPrograms')
                    ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                        $query
                            ->where('organization_id','=',$organizationId);
                    })
                    ->get();
            }else{
                return self::with('schoolPrograms')
                    ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                        $query
                            ->where('organization_id','=',$organizationId);
                    })
                    ->paginate($perPage);
            }

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
            return self::where('code',$code)
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
            return self::insertGetId($subject->only('code','name','uc','is_final_subject','is_project_subject',
                'theoretical_hours','practical_hours','laboratory_hours'));
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSubjectByCode($code,$organizationId)
    {
        try{
            return self::where('code',$code)
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

    public static function updateSubjectLikeArray($id,$subject)
    {
        self::find($id)
            ->update($subject);
        try{

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
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getProjectBySchoolProgram($schoolProgramId,$organizationId)
    {
        try{
            return self::where('is_project_subject',true)
                ->whereHas('schoolPrograms',function (Builder $query) use ($schoolProgramId,$organizationId){
                $query
                    ->where('organization_id','=',$organizationId)
                    ->where('school_program_id','=',$schoolProgramId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getFinalWorkBySchoolProgram($schoolProgramId, $organizationId)
    {
        try{
            return self::where('is_final_subject',true)
                ->whereHas('schoolPrograms',function (Builder $query) use ($schoolProgramId,$organizationId){
                    $query
                        ->where('organization_id','=',$organizationId)
                        ->where('school_program_id','=',$schoolProgramId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSimpleSubjectById($id,$organizationId){
        try{
            return self::where('id',$id)
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSubjectsWithoutFinalWorks($organizationId){
        try{
            return self::with('schoolPrograms')
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId);
                })
                ->where('is_final_subject','=',false)
                ->where('is_project_subject','=',false)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSubjectsInProgramsNotDegree($organizationId){
        try{
            return self::with('schoolPrograms')
                ->whereHas('schoolPrograms',function (Builder $query) use ($organizationId){
                    $query
                        ->where('organization_id','=',$organizationId)
                        ->where('conducive_to_degree','=',false);
                })
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }
}
