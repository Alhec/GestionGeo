<?php

namespace App\Services;

use App\Log;
use Illuminate\Http\Request;
use App\SchoolProgram;

class SchoolProgramService
{

    const taskError = 'No se puede proceder con la tarea';
    const emptyProgram = 'No existen programas asociados';
    const notFoundProgram = 'Programa no encontrado';
    const busyName = 'Nombre del programa en uso';
    const ok = 'OK';

    const logCreateSchoolProgram = 'Creo el programa escolar ';
    const logUpdateSchoolProgram = 'Actualizo el programa escolar ';
    const logDeleteSchoolProgram = 'Elimino el programa escolar ';

    public static function getSchoolProgram($organizationId)
    {
        $schoolPrograms = SchoolProgram::getSchoolProgram($organizationId);
        if (is_numeric($schoolPrograms)&&$schoolPrograms == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($schoolPrograms)>0){
            return $schoolPrograms;
        }
        return response()->json(['message'=>self::emptyProgram],206);
    }

    public static function getSchoolProgramById($id,$organizationId)
    {
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
            "conducive_to_degree"=>"required|boolean",
            'grant_certificate'=>'boolean',
            'doctoral_exam'=>'boolean',
            'min_cu_to_doctoral_exam'=>'numeric'
        ]);
    }

    public static function validateWithDegree(Request $request)
    {
        $request->validate([
            'num_cu'=>'required|numeric',
            'duration'=>'required|numeric',
            'min_duration'=>'numeric|required',
            'min_num_cu_final_work'=>'numeric|required',
        ]);
    }

    public static function validateWithoutDegree(Request $request)
    {
        $request->validate([
            'num_cu'=>'numeric',
            'duration'=>'numeric',
            'min_duration'=>'numeric',
            'min_num_cu_final_work'=>'numeric',
        ]);
    }

    public static function addSchoolProgram(Request $request,$organizationId)
    {
        self::validate($request);
        if ($request['conducive_to_degree']){
            self::validateWithDegree($request);
        }else{
            self::validateWithoutDegree($request);
        }
        $existSchoolProgramByName=SchoolProgram::existSchoolProgramByName($request['school_program_name'],$organizationId);
        if (is_numeric($existSchoolProgramByName)&& $existSchoolProgramByName==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (!$existSchoolProgramByName){
            $request['organization_id']=$organizationId;
            $id = SchoolProgram::addSchoolProgram($request);
            if (is_numeric($id) && $id == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logCreateSchoolProgram.$request['school_program_name']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskError],401);
            }
            return self::getSchoolProgramById($id,$organizationId);
        }
        return response()->json(['message'=>self::busyName],206);
    }

    public static function deleteSchoolProgram($id,$organizationId)
    {
        $schoolProgram = SchoolProgram::getSchoolProgramById($id,$organizationId);
        if (is_numeric($schoolProgram) && $schoolProgram == 0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($schoolProgram)>0){
            $result = SchoolProgram::deleteSchoolProgram($id);
            if (is_numeric($result) && $result == 0){
                return response()->json(['message'=>self::taskError],206);
            }
            $log = Log::addLog(auth('api')->user()['id'],self::logDeleteSchoolProgram.
                $schoolProgram[0]['school_program_name']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskError],401);
            }
            return response()->json(['message'=>self::ok]);
        }
        return response()->json(['message'=>self::notFoundProgram],206);
    }

    public static function updateSchoolProgram(Request $request, $id,$organizationId)
    {
        self::validate($request);
        if ($request['conducive_to_degree']){
            self::validateWithDegree($request);
        }else{
            self::validateWithoutDegree($request);
        }
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
            $log = Log::addLog(auth('api')->user()['id'],self::logUpdateSchoolProgram.$request['school_program_name']);
            if (is_numeric($log)&&$log==0){
                return response()->json(['message'=>self::taskError],401);
            }
            return self::getSchoolProgramById($id,$organizationId);
        }
        return response()->json(['message'=>self::notFoundProgram],206);
    }
}
