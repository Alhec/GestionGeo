<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolProgram extends Model
{
    //
    protected $fillable = ['school_program_name','num_cu','organization_id','duration'];

    protected $hidden = ['organization_id'];

    public $timestamps = false;

    public static function getSchoolProgram($organizationId)
    {
        return self::where('organization_id',$organizationId)
            ->get();
    }

    public static function getSchoolProgramById($id, $organization_id)
    {
        return self::where('id',$id)
            ->where('organization_id',$organization_id)
            ->get();
    }

    public static function existSchoolProgramByName($name, $organizationId)
    {
        return self::where('school_program_name',$name)
            ->where('organization_id',$organizationId)
            ->exists();
    }

    public static function addSchoolProgram($schoolProgram)
    {
        return self::insertGetId($schoolProgram->all());
    }

    public static function getSchoolProgramByName($name, $organizationId)
    {
        return self::where('school_program_name',$name)
            ->where('organization_id',$organizationId)
            ->get();
    }

    public static function existSchoolProgramById($id, $organizationId)
    {
        return self::where('id',$id)
            ->where('organization_id',$organizationId)
                ->exists();
    }

    public static function deleteSchoolProgram($id)
    {
        self::find($id)
            ->delete();
    }

    public static function updateSchoolProgram($id, $schoolProgram)
    {
        self::find($id)
            ->update($schoolProgram->all());
    }

}

