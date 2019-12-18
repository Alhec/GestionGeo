<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 05/10/19
 * Time: 04:29 PM
 */

namespace App\Services;

use App\Administrator;
use App\Organization;
use App\SchoolProgram;
use App\SchoolPeriodStudent;
use App\SchoolPeriodSubjectTeacher;
use App\Student;
use App\SchoolPeriod;
use App\StudentSubject;
use App\Teacher;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConstanceService
{

    public static function numberToMonth($month)
    {
        switch ($month){
            case 1:
                return 'enero';
            case 2:
                return 'febrero';
            case 3:
                return 'marzo';
            case 4:
                return 'abril';
            case 5:
                return 'mayo';
            case 6:
                return 'junio';
            case 7:
                return 'julio';
            case 8:
                return 'agosto';
            case 9:
                return 'septiembre';
            case 10:
                return 'octubre';
            case 11:
                return 'noviembre';
            case 12:
                return 'diciembre';
        }
    }

    public static function constanceOfStudy(Request $request, $studentId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId);
        if (count($student)>0){
            $user=User::getUserById($student[0]['user_id'],'S',$organizationId);
            if (count($user)>0){
                $data['user_data']=$user[0];
                $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
                $data['school_program_data']=SchoolProgram::getSchoolProgramById($user[0]['student']['school_program_id'],$organizationId)[0];
                $now = Carbon::now();
                $data['month']=self::numberToMonth($now->month);
                $data['year']=$now->year;
                if ($organizationId =='G'){
                    \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                    $pdf = \PDF::loadView('constance/Geoquimica/constancia_estudio',compact('data'));
                    return $pdf->download('constancia_estudio.pdf');
                }
                return $data;
            }

        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }

    public static function academicLoad(Request $request, $studentId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
        if (count($currentSchoolPeriod)>0 && Student::existStudentById($studentId)){
            $schoolPeriodStudent=SchoolPeriodStudent::findSchoolPeriodStudent($studentId,$currentSchoolPeriod[0]['id']);
            if (count($schoolPeriodStudent)>0){
                return$schoolPeriodStudent;
            }
            return response()->json(['message'=>'No tiene carga academica'],206);
        }
        return response()->json(['message'=>'No hay periodo escolar en curso o no existe el estudiante'],206);
    }

    public static function dataHistorical($historical)
    {
        $enrolledCredits=0;
        $cumulativeNotes=0;
        $cantSubjects=0;
        foreach ($historical as $schoolPeriod){
            foreach ($schoolPeriod['inscriptions']['enrolled_subjects'] as $inscription){
                if ($inscription['qualification']){
                    $enrolledCredits+=$inscription['data_subject']['subject']['uc'];
                    $cumulativeNotes+=$inscription['qualification'];
                    $cantSubjects +=1;
                }
            }
        }
        $dataHistorical = [];
        $dataHistorical['enrolled_credits']=$enrolledCredits;
        $dataHistorical['cumulative_notes']=$cumulativeNotes;
        $dataHistorical['cant_subjects']=$cantSubjects;
        $dataHistorical['porcentual']=$cumulativeNotes/$cantSubjects;
        return $dataHistorical;
    }

    public static function studentHistoricalAllData(Request $request, $studentId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId);
        if (count($student)>0){
            $user=User::getUserById($student[0]['user_id'],'S',$organizationId);
            if (count($user)>0){
                $data['user_data']=$user[0];
                $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
                $data['school_program_data']=SchoolProgram::getSchoolProgramById($user[0]['student']['school_program_id'],$organizationId)[0];
                $now = Carbon::now();
                $data['month']=self::numberToMonth($now->month);
                $data['year']=$now->year;
                $studentSubject =self::clearHistoricalByStudentId(SchoolPeriod::getEnrolledSubjectsByStudent($studentId)->toArray(),$studentId);
                if (count($studentSubject)>0){
                    $data['historical_data']=$studentSubject;
                    $data['porcentual_data']=self::dataHistorical($studentSubject);
                    if ($organizationId =='G'){
                        \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                        $pdf = \PDF::loadView('constance/Geoquimica/constancia_notas',compact('data'));
                        return $pdf->download('constancia_nota.pdf');
                    }
                    return $data;
                }
                return response()->json(['message'=>'Aun no tiene historial'],206);
            }
        }
        return response()->json(['message'=>'No existe el estudiante'],206);
    }

    public static function clearHistoricalByStudentId($studentSubject,$studentId)
    {
        $studentSubjectReturn = [];
        foreach ($studentSubject as $schoolPeriod){
            $inscriptionReturn=[];
            foreach ($schoolPeriod['inscriptions'] as $inscription){
                if ($inscription['student_id'] ==$studentId){
                    $inscriptionReturn=$inscription;
                }
            }
            unset($schoolPeriod['inscriptions']);
            $schoolPeriod['inscriptions']=$inscriptionReturn;
            $studentSubjectReturn[]=$schoolPeriod;
        }
        return $studentSubjectReturn;
    }

    public static function studentHistorical(Request $request, $studentId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            //$isValid = StudentService::validateStudent($request)eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9sb2dpbiIsImlhdCI6MTU3NjE1Nzk1OCwiZXhwIjoxNTc2MTkzOTU4LCJuYmYiOjE1NzYxNTc5NTgsImp0aSI6ImhBeUk1dlhXdlVlUWdXbmEiLCJzdWIiOjEsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEiLCJ1c2VyIjp7ImlkIjoxLCJpZGVudGlmaWNhdGlvbiI6IjI0Njk4OTE2IiwiZmlyc3RfbmFtZSI6IkhlY3RvciIsInNlY29uZF9uYW1lIjpudWxsLCJmaXJzdF9zdXJuYW1lIjoiQWxheW9uIiwic2Vjb25kX3N1cm5hbWUiOm51bGwsInRlbGVwaG9uZSI6bnVsbCwibW9iaWxlIjoiKDEyMzQpIDU2Ny04OTAxIiwid29ya19waG9uZSI6bnVsbCwiZW1haWwiOiJoZWN0b3JAYWRtaW4uY29tIiwidXNlcl90eXBlIjoiQSIsImxldmVsX2luc3RydWN0aW9uIjoiRHIiLCJhY3RpdmUiOjEsIndpdGhfZGlzYWJpbGl0aWVzIjowLCJzZXgiOiJNIiwibmF0aW9uYWxpdHkiOiJWIiwib3JnYW5pemF0aW9uX2lkIjoiRyIsImFkbWluaXN0cmF0b3IiOnsiaWQiOjEsInJvbCI6IkNPT1JESU5BVE9SIiwicHJpbmNpcGFsIjoxfX19.4V9Ni7Vuh0LFJo_lsFS3e8zOswW929H29hAVZtKSdS8
            //if ($isValid!='valid'){
               // return $isValid;
           // }
        }
        $student = Student::getStudentById($studentId);
        if (count($student)>0){
            if (User::existUserById($student[0]['user_id'],'S',$organizationId)){
                $studentSubject =SchoolPeriod::getEnrolledSubjectsByStudent($studentId);
                if (count($studentSubject)>0){
                    return self::clearHistoricalByStudentId($studentSubject,$studentId);
                }
                return response()->json(['message'=>'Aun no tiene historial'],206);
            }
        }
        return response()->json(['message'=>'No existe el estudiante'],206);
    }

    public static function inscriptionConstance(Request $request, $studentId,$inscriptionId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId);
        if (count($student)>0){
            $user=User::getUserById($student[0]['user_id'],'S',$organizationId);
            if (count($user)>0){
                $inscription = SchoolPeriodStudent::getSchoolPeriodStudentById($inscriptionId,$organizationId)->toArray();
                if (count($inscription)>0){
                    if ($inscription[0]['student_id']==$studentId){
                        $data['user_data']=$user[0];
                        $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
                        $data['school_program_data']=SchoolProgram::getSchoolProgramById($user[0]['student']['school_program_id'],$organizationId)[0];
                        $now = Carbon::now();
                        $data['month']=self::numberToMonth($now->month);
                        $data['year']=$now->year;
                        $studentSubject =self::clearHistoricalByStudentId(SchoolPeriod::getEnrolledSubjectsByStudent($studentId)->toArray(),$studentId);
                        if (count($studentSubject)>0){
                            $data['historical_data']=$studentSubject;
                            $data['porcentual_data']=self::dataHistorical($studentSubject);
                        } else {
                            $dataHistorical['enrolled_credits']=0;
                            $dataHistorical['cumulative_notes']=0;
                            $dataHistorical['cant_subjects']=0;
                            $dataHistorical['porcentual']=0;
                            $data['porcentual_data']=$dataHistorical;
                        }
                        $data['inscription']=$inscription[0];
                        if ($organizationId =='G'){
                            \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                            $pdf = \PDF::loadView('constance/Geoquimica/constancia_inscripcion',compact('data'));
                            return $pdf->download('inscripcion.pdf');
                        }
                        return $data;

                    }
                }return response()->json(['message'=>'Inscripcion no encontrada'],206);

            }
        }
        return response()->json(['message'=>'No existe el estudiante'],206);
    }

    public static function teacherHistorical(Request $request, $teacherId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['teacher_id']=$teacherId;
            $isValid = TeacherService::validateTeacher($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $teacher = Teacher::getTeacherById($teacherId);
        if (count($teacher)>0){
            if (User::existUserById($teacher[0]['user_id'],'T',$organizationId)){
                $schoolPeriodSubjectTeacher = SchoolPeriod::getSubjectsByTeacher($teacherId);
                if (count($schoolPeriodSubjectTeacher)>0){
                    return $schoolPeriodSubjectTeacher;
                }
                return response()->json(['message'=>'Aun no tiene historial'],206);
            }
        }
        return response()->json(['message'=>'No existe el profesor'],206);
    }

    public static function constanceOfWorkTeacher(Request $request, $teacherId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            $request['teacher_id']=$teacherId;
            $isValid = TeacherService::validateTeacher($request);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $teacher = Teacher::getTeacherById($teacherId);
        if (count($teacher)>0){
            $data['user_data']=UserService::getUserById($request,$teacher[0]['user_id'],'T');
            $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
            $data['organization_data']=Organization::getOrganization($organizationId);
            return $data;
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }

    public static function constanceOfWorkAdministrator(Request $request, $administratorId)
    {
        $organizationId = $request->header('organization_key');
        if ((auth()->payload()['user'][0]->user_type)!='A'){
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $data=[];
        $administrator = Administrator::getAdministratorById($administratorId);
        if (count($administrator)>0){
            $data['user_data']=UserService::getUserById($request,$administrator[0]['user_id'],'A');
            $data['coordinator_data']=AdministratorService::getPrincipalCoordinator($request);
            $data['organization_data']=Organization::getOrganization($organizationId);
            return $data;
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }
}
