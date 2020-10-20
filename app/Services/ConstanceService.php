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

    public static function numberToDay($day)
    {
        switch ($day){
            case 1:
                return 'al primer día ';
            case 2:
                return 'al segundo día ';
            case 3:
                return 'al tercer día ';
            case 4:
                return 'al cuarto día ';
            case 5:
                return 'al quinto día ';
            case 6:
                return 'a los seis días ';
            case 7:
                return 'a los siete días ';
            case 8:
                return 'a los ocho días ';
            case 9:
                return 'a los nueve días ';
            case 10:
                return 'a los diez días ';
            case 11:
                return 'a los once días ';
            case 12:
                return 'a los doce días ';
            case 13:
                return 'a los trece días ';
            case 14:
                return 'a los catorce días ';
            case 15:
                return 'a los quince días ';
            case 16:
                return 'a los dieciséis días ';
            case 17:
                return 'a los diecisiete días ';
            case 18:
                return 'a los dieciocho días ';
            case 19:
                return 'a los diecinueve días ';
            case 20:
                return 'a los veinte días ';
            case 21:
                return 'a los veintiuno días ';
            case 22:
                return 'a los veintidós días ';
            case 23:
                return 'a los veintitrés días ';
            case 24:
                return 'a los veinticuatro días ';
            case 25:
                return 'a los veinticinco días ';
            case 26:
                return 'a los veintiséis días ';
            case 27:
                return 'a los veintisiete días ';
            case 28:
                return 'a los veintiocho días ';
            case 29:
                return 'a los veintinueve días ';
            case 30:
                return 'a los treinta días ';
            default:
                return 'a los treinta y un días ';
        }
    }

    public static function constanceOfStudy($studentId,$organizationId,$getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
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
            $data['day']=self::numberToDay($now->day);
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
        $dataHistorical['percentage']=$cantSubjects==0?0:$cumulativeNotes/$cantSubjects;
        return $dataHistorical;
    }

    public static function countCreditsEquivalences($creditsGranted, $equivalences)
    {
        $count = $creditsGranted;
        foreach ($equivalences as $equivalence){
            $count = $count+$equivalence['subject']['uc'];
        }
        return $count;
    }

    public static function inscriptionConstance($studentId,$inscriptionId,$organizationId, $getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
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
            $inscription = SchoolPeriodStudent::getSchoolPeriodStudentById($inscriptionId,$organizationId);
            if (is_numeric($inscription)&&$inscription===0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($inscription)>0){
                if ($inscription[0]['student_id']==$studentId){
                    $data['user_data']=$student;
                    $data['extra_credits']=self::countCreditsEquivalences($student['credits_granted'],
                        $student['equivalence']);
                    $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
                    if (is_numeric($schoolProgram)&&$schoolProgram===0){
                        return response()->json(['message'=>self::taskError],206);
                    }
                    $data['school_program_data']=$schoolProgram->toArray()[0];
                    $now = Carbon::now();
                    $data['month']=self::numberToMonth($now->month);
                    $data['year']=$now->year;
                    $data['inscription']=$inscription->toArray()[0];
                    $studentSubject=SchoolPeriodStudent::getEnrolledSchoolPeriodsByStudent($studentId,$organizationId);
                    if (is_numeric($studentSubject)&&$studentSubject===0){
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

    public static function constanceOfWorkTeacher($teacherId,$organizationId,$getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            $isValid = TeacherService::validateTeacher($teacherId,$organizationId);
            if ($isValid!='valid'){
                return $isValid;
            }
        }
        $data=[];
        $teacher = User::getUserById($teacherId,'T',$organizationId);
        if (is_numeric($teacher)&&$teacher===0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($teacher)>0){
            $schoolPeriods = SchoolPeriod::getSubjectsByTeacher($teacherId);
            if (is_numeric($schoolPeriods)&&$schoolPeriods===0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($schoolPeriods)>0){
                foreach ($schoolPeriods as $schoolPeriod ){
                    $subjects=$schoolPeriod['subjects'];
                    for($i=0; $i<count($subjects);$i++){
                        if ($subjects[$i]['teacher_id']!=$teacherId){
                            unset($subjects[$i]);
                        }
                    }
                    $schoolPeriod['subjects']=$subjects;
                }
                $data['user_data']=$teacher[0]->toArray();
                $data['historical_data']=$schoolPeriods->toArray();
                $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
                if (is_numeric($coordinator)&&$coordinator===0){
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
                if ($getData==="1"){
                    return $data;
                }
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

    public static function constanceOfWorkAdministrator($administratorId,$organizationId,$getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
            return response()->json(['message'=>'Unauthorized'],401);
        }
        $data=[];
        $administrator =User::getUserById($administratorId,'A',$organizationId);
        if (is_numeric($administrator)&&$administrator===0){
            return response()->json(['message'=>self::taskError],206);
        }
        if (count($administrator)>0){
            $data['user_data']=$administrator[0]->toArray();
            $organization=Organization::getOrganizationById($organizationId);
            if (is_numeric($organization)&&$organization===0){
                return response()->json(['message'=>self::taskError],206);
            }
            $data['organization_data']=$organization[0]->toArray();
            $coordinator=AdministratorService::getPrincipalCoordinator($organizationId,true);
            if (is_numeric($coordinator)&&$coordinator===0){
                return response()->json(['message'=>self::taskError],206);
            }
            if ($coordinator=='noExist'){
                return response()->json(['message'=>self::hasNotPrincipal],206);
            }
            $data['coordinator_data']=$coordinator->toArray();
            $now = Carbon::now();
            $data['month']=self::numberToMonth($now->month);
            $data['year']=$now->year;
            $data['day']=self::numberToDay($now->day);
            if ($getData==="1"){
                return $data;
            }
            if ($organizationId =='ICT'){
                \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                $pdf = \PDF::loadView('constance/Geoquimica/constancia_trabajo_administrador',compact('data'));
                return $pdf->download('constancia_trabajo.pdf');
            }
            return $data;
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function cmpSubjectCode($a, $b)
    {
        return strcmp($a["code"], $b["code"]);
    }

    public static function countCreditsOfSubjects($subjects){
        $total = 0;
        foreach ($subjects as $subject){
            $total +=$subject['uc'];
        }
        return $total;
    }

    public static function academicLoad( $studentId,$organizationId,$getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
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
            $subjects = StudentSubject::getAllSubjectsEnrolledWithoutRET($studentId);
            if (is_numeric($subjects)&&$subjects===0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($subjects)>0){
                $subjects = array_column(array_column( $subjects->toArray(),'data_subject'),'subject');
                usort($subjects,'self::cmpSubjectCode');
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
                $data['school_program_data']=$schoolProgram->toArray()[0];
                $now = Carbon::now();
                $data['month']=self::numberToMonth($now->month);
                $data['year']=$now->year;
                $data['day']=$now->day;
                $data['subjects_data']=$subjects;
                $data['total_credits']=self::countCreditsOfSubjects($subjects);
                if ($getData==="1"){
                    return $data;
                }
                if ($organizationId =='ICT'){
                    \PDF::setOptions(['isHtml5ParserEnabled' => true]);
                    $pdf = \PDF::loadView('constance/Geoquimica/carga_academica',compact('data'));
                    return $pdf->download('carga_academica.pdf');
                }
                return $data;
            }
            return response()->json(['message'=>self::notFoundInscription],206);
        }
        return response()->json(['message'=>self::notFoundUser],206);
    }

    public static function studentHistorical( $studentId,$organizationId,$getData)
    {
        if ((auth()->payload()['user']->user_type)!='A'){
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
            $enrolledSubjects = SchoolPeriodStudent::getEnrolledSchoolPeriodsByStudent($studentId,$organizationId);
            if (is_numeric($enrolledSubjects)&&$enrolledSubjects===0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($enrolledSubjects)>0){
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
                $data['school_program_data']=$schoolProgram->toArray()[0];
                $now = Carbon::now();
                $data['month']=self::numberToMonth($now->month);
                $data['year']=$now->year;
                $data['day']=$now->day;
                foreach ($enrolledSubjects as $enrolledSubject){
                    if (count($enrolledSubject['finalWorkData'])>0){
                        $i=0;
                        foreach ($enrolledSubject['finalWorkData'] as $finalWork){
                            if ($finalWork['status']=='APPROVED'){
                                $i++;
                            }
                        }
                        $enrolledSubject['cant_subjects']=$i + count($enrolledSubject['enrolledSubjects']);
                    }else{
                        $enrolledSubject['cant_subjects']=0 + count($enrolledSubject['enrolledSubjects']);
                    }
                }
                $data['enrolled_subjects']=$enrolledSubjects->toArray();
                $data['percentage_data']=self::statisticsDataHistorical($enrolledSubjects);
                if ($getData==="1"){
                    return $data;
                }
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

}
