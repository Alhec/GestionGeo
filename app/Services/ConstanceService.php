<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 05/10/19
 * Time: 04:29 PM
 */

namespace App\Services;

use App\Organization;
use App\SchoolProgram;
use App\SchoolPeriodStudent;
use App\Student;
use App\SchoolPeriod;
use App\StudentSubject;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConstanceService
{

    const taskError = 'No se puede proceder con la tarea';
    const notFoundUser = 'Usuario no encontrado';
    const hasNotPrincipal = 'No hay coordinador principal';
    const notFoundInscription = 'Inscripcion no encontrada';
    const notYetHaveHistorical = 'Aun no tienes historial';

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

    public static function constanceOfStudy(Request $request, $studentId,$organizationId,$getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($organizationId,$studentId);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId,$organizationId);
        if (is_numeric($student)&&$student===0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($student)>0){
            $student=$student[0]->toArray();
            $data['user_data']=$student;
            $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
            if (is_numeric($coordinator)&&$coordinator===0){
                return response()->json(['message'=>self::taskError],206);
            }
            if ($coordinator=='noExist'){
                return response()->json(['message'=>self::hasNotPrincipal],206);
            }
            $data['coordinator_data']=$coordinator->toArray();
            $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
            if (is_numeric($schoolProgram)&&$schoolProgram===0){
                return response()->json(['message'=>self::taskError],206);
            }
            $data['school_program_data']=$schoolProgram[0]->toArray();
            $now = Carbon::now();
            $data['month']=self::numberToMonth($now->month);
            $data['year']=$now->year;
            if ($getData==="1"){
                return $data;
            }
            if ($organizationId =='ICT'){
                \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                $pdf = \PDF::loadView('constance/Geoquimica/constancia_estudio',compact('data'));
                return $pdf->download('constancia_estudio.pdf');
            }
            return $data;
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function statisticsDataHistorical($historical)
    {
        $enrolledCredits=0;
        $cumulativeNotes=0;
        $cantSubjects=0;
        foreach ($historical->toArray() as $schoolPeriod){
            foreach ($schoolPeriod['enrolled_subjects'] as $inscription){
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
        $dataHistorical['percentage']=$cumulativeNotes/$cantSubjects;
        return $dataHistorical;
    }

    public static function inscriptionConstance(Request $request, $studentId,$inscriptionId,$organizationId, $getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($organizationId,$studentId);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId,$organizationId);
        if (is_numeric($student)&&$student==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($student)>0){
            $student=$student[0]->toArray();
            $inscription = SchoolPeriodStudent::getSchoolPeriodStudentById($inscriptionId,$organizationId);
            if (is_numeric($inscription)&&$inscription==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($inscription)>0){
                if ($inscription[0]['student_id']==$studentId){
                    $data['user_data']=$student;
                    $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
                    if (is_numeric($schoolProgram)&&$schoolProgram==0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                    $data['school_program_data']=$schoolProgram->toArray()[0];
                    $now = Carbon::now();
                    $data['month']=self::numberToMonth($now->month);
                    $data['year']=$now->year;
                    $data['inscription']=$inscription->toArray()[0];
                    $studentSubject=SchoolPeriodStudent::getEnrolledSchoolPeriodsByStudent($studentId,$organizationId);
                    if (is_numeric($studentSubject)&&$studentSubject==0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                    if (count($studentSubject)>0){
                        $data['historical_data']=$studentSubject;
                        $data['percentage_data']=self::statisticsDataHistorical($studentSubject);
                    } else {
                        $data['historical_data']=[];
                        $dataHistorical['enrolled_credits']=0;
                        $dataHistorical['cumulative_notes']=0;
                        $dataHistorical['cant_subjects']=0;
                        $dataHistorical['percentage']=0;
                        $data['percentage_data']=$dataHistorical;
                    }
                    if ($getData==="1"){
                        return $data;
                    }
                    if ($organizationId =='ICT'){
                        \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                        $pdf = \PDF::loadView('constance/Geoquimica/constancia_inscripcion',compact('data'));
                        return $pdf->download('inscripcion.pdf');
                    }
                    return $data;
                }
            }
            return response()->json(['message'=>self::notFoundInscription],206);
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function cmpSubjectCode($a, $b)
    {
        return strcmp($a["subject_code"], $b["subject_code"]);
    }

    public static function countCreditsOfSubjects($subjects){
        $total = 0;
        foreach ($subjects as $subject){
            $total +=$subject['uc'];
        }
        return $total;
    }

    public static function academicLoad(Request $request, $studentId,$organizationId)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($organizationId,$studentId);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId,$organizationId);
        if (is_numeric($student)&&$student==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($student)>0){
            $student=$student[0]->toArray();
            $subjects = StudentSubject::getAllSubjectsEnrolledWithoutRET($studentId);
            if (is_numeric($subjects)&&$subjects==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($subjects)>0){
                $subjects = array_column(array_column( $subjects->toArray(),'data_subject'),'subject');
                usort($subjects,'self::cmpSubjectCode');
                $data['user_data']=$student;
                $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
                if (is_numeric($coordinator)&&$coordinator==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                if ($coordinator=='noExist'){
                    return response()->json(['message'=>self::hasNotPrincipal],206);
                }
                $data['coordinator_data']=$coordinator->toArray();
                $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
                if (is_numeric($schoolProgram)&&$schoolProgram==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                $data['school_program_data']=$schoolProgram->toArray()[0];
                $now = Carbon::now();
                $data['month']=self::numberToMonth($now->month);
                $data['year']=$now->year;
                $data['day']=$now->day;
                $data['subjects_data']=$subjects;
                $data['total_credits']=self::countCreditsOfSubjects($subjects);
                if ($organizationId =='ICT'){
                    \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                    $pdf = \PDF::loadView('constance/Geoquimica/carga_academica',compact('data'));
                    return $pdf->download('carga_academica.pdf');
                }
                return $data;



            }return response()->json(['message'=>self::notFoundInscription],206);
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function studentHistorical(Request $request, $studentId,$organizationId)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($organizationId,$studentId);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId,$organizationId);
        if (is_numeric($student)&&$student==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($student)>0){
            $student=$student[0]->toArray();
            $enrolledSubjects = SchoolPeriodStudent::getEnrolledSchoolPeriodsByStudent($studentId,$organizationId);
            if (is_numeric($enrolledSubjects)&&$enrolledSubjects==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($enrolledSubjects)>0){
                $data['user_data']=$student;
                $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
                if (is_numeric($coordinator)&&$coordinator==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                if ($coordinator=='noExist'){
                    return response()->json(['message'=>self::hasNotPrincipal],206);
                }
                $data['coordinator_data']=$coordinator->toArray();
                $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
                if (is_numeric($schoolProgram)&&$schoolProgram==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                $data['school_program_data']=$schoolProgram->toArray()[0];
                $now = Carbon::now();
                $data['month']=self::numberToMonth($now->month);
                $data['year']=$now->year;
                $data['day']=$now->day;
                $data['enrolled_subjects']=$enrolledSubjects->toArray();
                $data['porcentual_data']=self::statisticsDataHistorical($enrolledSubjects);
                if ($organizationId =='ICT'){
                    \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                    $pdf = \PDF::loadView('constance/Geoquimica/constancia_notas',compact('data'));
                    return $pdf->download('constancia_notas.pdf');
                }
                return $data;
            }
            return response()->json(['message'=>self::notYetHaveHistorical],206);
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function studentHistoricalData(Request $request, $studentId,$organizationId)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            $request['student_id']=$studentId;
            $isValid = StudentService::validateStudent($organizationId,$studentId);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $student = Student::getStudentById($studentId,$organizationId);
        if (is_numeric($student)&&$student==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($student)>0){
            $student=$student[0]->toArray();
            $enrolledSubjects = SchoolPeriodStudent::getEnrolledSchoolPeriodsByStudent($studentId,$organizationId);
            if (is_numeric($enrolledSubjects)&&$enrolledSubjects==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($enrolledSubjects)>0){
                $data['user_data']=$student;
                $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
                if (is_numeric($coordinator)&&$coordinator==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                if ($coordinator=='noExist'){
                    return response()->json(['message'=>self::hasNotPrincipal],206);
                }
                $data['coordinator_data']=$coordinator->toArray();
                $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
                if (is_numeric($schoolProgram)&&$schoolProgram==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                $data['school_program_data']=$schoolProgram->toArray()[0];
                $data['enrolled_subjects']=$enrolledSubjects->toArray();
                $data['porcentual_data']=self::statisticsDataHistorical($enrolledSubjects);
                return $data;
            }
            return response()->json(['message'=>self::notYetHaveHistorical],206);
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function constanceOfWorkTeacher(Request $request, $teacherId,$organizationId)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            $request['teacher_id']=$teacherId;
            $isValid = TeacherService::validateTeacher($teacherId,$organizationId);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $teacher = User::getUserById($teacherId,'T',$organizationId);
        if (is_numeric($teacher)&&$teacher==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($teacher)>0){
            $schoolPeriod = SchoolPeriod::getSubjectsByTeacher($teacherId);
            if (is_numeric($schoolPeriod)&&$schoolPeriod==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($schoolPeriod)>0){
                $data['user_data']=$teacher[0]->toArray();
                $data['historical_data']=$schoolPeriod->toArray();
                $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
                if (is_numeric($coordinator)&&$coordinator==0){
                    return response()->json(['message'=>self::taskError],206);
                }
                if ($coordinator=='noExist'){
                    return response()->json(['message'=>self::hasNotPrincipal],206);
                }
                $data['coordinator_data']=$coordinator->toArray();
                $now = Carbon::now();
                $data['month']=self::numberToMonth($now->month);
                $data['year']=$now->year;
                $data['day']=$now->day;
                if ($organizationId =='ICT'){
                    \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                    $pdf = \PDF::loadView('constance/Geoquimica/constancia_trabajo_profesor',compact('data'));
                    return $pdf->download('constancia_trabajo.pdf');
                }
                return $data;
            }
            return response()->json(['message'=>self::notYetHaveHistorical],206);
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function constanceOfWorkAdministrator(Request $request, $administratorId,$organizationId)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $data=[];
        $administrator =User::getUserById($administratorId,'A',$organizationId);
        if (is_numeric($administrator)&&$administrator==0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($administrator)>0){
            $data['user_data']=$administrator[0]->toArray();
            $organization=Organization::getOrganizationById($organizationId);
            if (is_numeric($organization)&&$organization==0){
                return response()->json(['message'=>self::taskError],206);
            }
            $data['organization_data']=$organization[0]->toArray();
            $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
            if (is_numeric($coordinator)&&$coordinator==0){
                return response()->json(['message'=>self::taskError],206);
            }
            if ($coordinator=='noExist'){
                return response()->json(['message'=>self::hasNotPrincipal],206);
            }
            $data['coordinator_data']=$coordinator->toArray();
            $now = Carbon::now();
            $data['month']=self::numberToMonth($now->month);
            $data['year']=$now->year;
            $data['day']=$now->day;
            if ($organizationId =='ICT'){
                \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                $pdf = \PDF::loadView('constance/Geoquimica/constancia_trabajo_administrador',compact('data'));
                return $pdf->download('constancia_trabajo.pdf');
            }
            return $data;
        }
        return response()->json(['message'=>'Usuario no encontrado'],206);
    }
}
