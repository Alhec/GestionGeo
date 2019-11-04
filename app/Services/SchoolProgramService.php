<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\SchoolProgram;
use App\Organization;

class SchoolProgramService
{

    const taskError = 'No se puede proceder con la tarea';
    const emptyProgram = 'No existen programas asociados';
    const notFoundProgram = 'Programa no encontrado';
    const busyName = 'Nombre del programa en uso';
    const ok = 'OK';

    public static function getSchoolProgram(Request $request)
    {
        $organizationId = $request->header('organization_key');
        $schoolPrograms = SchoolProgram::getSchoolProgram($organizationId);
        if (is_numeric($schoolPrograms)&&$schoolPrograms == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($schoolPrograms)>0){
            return $schoolPrograms;
        }
        return response()->json(['message'=>self::emptyProgram],206);
    }

    public static function getSchoolProgramById(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $schoolProgram = SchoolProgram::getSchoolProgramById($id,$organizationId);
        if (is_numeric($schoolProgram) && $schoolProgram == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($schoolProgram)>0) {
            return $schoolProgram[0];
        }
        return response()->json(['message'=>self::notFoundProgram],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'school_program_name'=>'required|max:100',
            'num_cu'=>'required|numeric',
            'duration'=>'required|numeric',
            "conducive_to_degree"=>"required|boolean"
        ]);
    }

    public static function addSchoolProgram(Request $request)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        if (!SchoolProgram::existSchoolProgramByName($request['school_program_name'],$organizationId)){
            $request['organization_id']=$organizationId;
            $id = SchoolProgram::addSchoolProgram($request);
            if ($id == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            return self::getSchoolProgramById($request,$id);
        }
        return response()->json(['message'=>self::busyName],206);
    }

    public static function deleteSchoolProgram(Request $request, $id)
    {
        $organizationId = $request->header('organization_key');
        $existSchoolProgram = SchoolProgram::existSchoolProgramById($id,$organizationId);
        if (is_numeric($existSchoolProgram) && $existSchoolProgram == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($existSchoolProgram){
            $result = SchoolProgram::deleteSchoolProgram($id);
            if (is_numeric($result) && $result == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            return response()->json(['message'=>self::ok]);
        }
        return response()->json(['message'=>self::notFoundProgram],206);
    }

    public static function updateSchoolProgram(Request $request, $id)
    {
        self::validate($request);
        $organizationId = $request->header('organization_key');
        $existSchoolProgram = SchoolProgram::existSchoolProgramById($id,$organizationId);
        if (is_numeric($existSchoolProgram) && $existSchoolProgram == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if ($existSchoolProgram){
            $request['organization_id']=$organizationId;
            $schoolProgramName=SchoolProgram::getSchoolProgramByName($request['school_program_name'],$organizationId);
            if (is_numeric($schoolProgramName) && $schoolProgramName == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($schoolProgramName)>0){
                if ($schoolProgramName[0]['id']!=$id){
                    return response()->json(['message'=>self::busyName],206);
                }
            }
            $result = SchoolProgram::updateSchoolProgram($id,$request);
            if (is_numeric($result) && $result == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            return self::getSchoolProgramById($request,$id);
        }
        return response()->json(['message'=>self::notFoundProgram],206);
    }
}