<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SchoolProgram extends Model
{
    protected $fillable = ['organization_id','school_program_name','num_cu', 'min_num_cu_final_work','duration',
        'min_duration','grant_certificate','conducive_to_degree','doctoral_exam'];

    protected $hidden = ['organization_id'];

    public $timestamps = false;

    public static function getSchoolProgram($organizationId)
    {
        try{
            return self::where('organization_id',$organizationId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function getSchoolProgramById($id, $organizationId)
    {
        try{
            return self::where('id',$id)
                ->where('organization_id',$organizationId)
                ->get();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function existSchoolProgramByName($name, $organizationId)
    {
        try{
            return self::where('school_program_name',$name)
                ->where('organization_id',$organizationId)
                ->exists();
        }catch (\Exception $e){
            return 0;
        }
    }

    public static function addSchoolProgram($schoolProgram)
    {
        try{
            return self::insertGetId($schoolProgram->all());
        }catch (\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function getSchoolProgramByName($name, $organizationId)
    {
        try{
            return self::where('school_program_name',$name)
                ->where('organization_id',$organizationId)
                ->get();
        }catch(\Exception $e){
            return 0;
        }
    }

    public static function existSchoolProgramById($id, $organizationId)
    {
        try{
            return self::where('id',$id)
                ->where('organization_id',$organizationId)
                ->exists();
        }catch(\Exception $e){
            return 0;
        }
    }

    public static function deleteSchoolProgram($id)
    {
        try{
            self::find($id)
                ->delete();
        }catch(\Exception $e){
            DB::rollback();
            return 0;
        }
    }

    public static function updateSchoolProgram($id, $schoolProgram)
    {
        try{
            self::find($id)
                ->update($schoolProgram->all());
        }catch(\Exception $e){
            DB::rollback();
            return 0;
        }
    }

}

