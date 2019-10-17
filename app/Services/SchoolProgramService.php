<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\SchoolProgram;
use App\Organization;

class SchoolProgramService
{

    public static function getSchoolProgram(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $schoolPrograms = SchoolProgram::getSchoolProgram($organizationId);
        if (count($schoolPrograms)>0){
            return $schoolPrograms;
        }
        return response()->json(['message'=>'No existen programas asociados'],206);
    }

    public static function getSchoolProgramById(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $schoolProgram = SchoolProgram::getSchoolProgramById($id,$organizationId);
        if (count($schoolProgram)>0) {
            return $schoolProgram[0];
        }
        return response()->json(['message'=>'Programa no encontrado'],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'school_program_name'=>'required|max:100',
            'num_cu'=>'required|numeric',
            'duration'=>'required|numeric',
        ]);
    }

    public static function addSchoolProgram(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (Organization::existOrganization($organizationId)){
            if (!SchoolProgram::existSchoolProgramByName($request['school_program_name'],$organizationId)){
                $request['organization_id']=$organizationId;
                $id = SchoolProgram::addSchoolProgram($request);
                return self::getSchoolProgramById($request,$id);
            }
            return response()->json(['message'=>'Nombre del programa en uso'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);
    }

    public static function deleteSchoolProgram(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        if (SchoolProgram::existSchoolProgramById($id,$organizationId)){
            SchoolProgram::deleteSchoolProgram($id);
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Programa no encontrado'],206);
    }

    public static function updateSchoolProgram(Request $request, $id)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (Organization::existOrganization($organizationId)){
            if (SchoolProgram::existSchoolProgramById($id,$organizationId)){
                $request['organization_id']=$organizationId;
                $schoolProgramName=SchoolProgram::getSchoolProgramByName($request['school_program_name'],$organizationId);
                if (count($schoolProgramName)>0){
                    if ($schoolProgramName[0]['id']!=$id){
                        return response()->json(['message'=>'Nombre del programa en uso'],206);
                    }
                }
                SchoolProgram::updateSchoolProgram($id,$request);
                return self::getSchoolProgramById($request,$id);
            }
            return response()->json(['message'=>'Programa no encontrado'],206);
        }
        return response()->json(['message'=>'No existe organizacion asociada'],206);
    }
}
