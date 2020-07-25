<?php
/**
 * Created by PhpStorm.
 * User: halayon
 * Date: 30/08/19
 * Time: 09:08 AM
 */

namespace App\Services;

use App\Advisor;
use App\DoctoralExam;
use App\FinalWork;
use App\FinalWorkSchoolPeriod;
use App\Log;
use App\SchoolProgram;
use App\Subject;
use Illuminate\Http\Request;
use App\SchoolPeriodStudent;
use App\Student;
use App\SchoolPeriod;
use App\SchoolPeriodSubjectTeacher;
use App\StudentSubject;
use PhpParser\Comment\Doc;


class InscriptionService
{

    const taskError = 'No se puede proceder con la tarea';
    const taskPartialError = 'No se pudo proceder con la tarea en su totalidad';
    const emptyInscriptions ='No hay inscripciones';
    const notFoundInscription = 'Inscripcion no encontrada';
    const emptyInscriptionInCurrentSchoolPeriod = 'Periodo escolar no posee inscripciones';
    const OK = 'OK';
    const notFoundStudentGivenId = 'No existe el estudiante dado el id';
    const thereAreNotSubjectsAvailableToRegister = 'No hay materias disponibles para inscribir';
    const thereAreSchoolPeriodWithoutPaying = 'Hay periodo escolar sin pagar';
    const warningAverage = 'tu promedio es inferior a 14, te sugerimos que vuelvas a ver una materia reprobada o una 
    materia ya aprobada si no lo has hecho antes, te encuentras en periodo de prueba en este semestre';
    const notAllowedRegister = 'No tienes permitido inscribir';
    const endProgram = 'Ya culmino el programa';
    const noCurrentSchoolPeriod='No hay periodo escolar en curso';
    const invalidSubject = 'Materia invalida';
    const notAvailableInscriptions = 'No estan disponibles las inscripciones';
    const inscriptionReady = 'Inscripcion ya realizada';
    const invalidSubjectToInscription = 'Las materias a inscribir no estan disponibles';
    const invalidData = 'Datos invalidos o no esta disponible la carga de notas';
    const busySchoolPeriodStudent ='El estudiante ya esta inscrito en el periodo escolar y se encuentra en otro registro';
    const expiredDate = 'No se puede realizar retiros la fecha ya ha pasado';
    const notCurrentInscription ='No hay inscripcion actual para usted';
    const notRegister = 'Debes mandar un body valido para inscribir';
    const notFoundFinalWork = 'Proyecto o trabajo de grado no encontrada';

    const logAddInscription = 'Inscripcion al estudiante con id ';
    const logUpdateInscription = 'Actualiza inscripcion al estudiante con id ';
    const logDeleteInscription = 'elimina inscripcion al estudiante con id ';
    const logWithdrawSubject = 'Ha retirado materias el estudiante con id ';
    const logLoadNotes = 'Realizo carga de notas';
    const logDeleteFinalWork = 'Elimina trabajo de grado o proyecto al estudiante con id ';

    public static function taskError($internalCall,$isPartial)
    {
        if ($internalCall){
            return 0;
        }
        if ($isPartial){
            return response()->json(['message' => self::taskPartialError], 206);
        }
        return response()->json(['message' => self::taskError], 206);
    }

    public static function getInscriptions($organizationId)
    {
        $inscriptions = SchoolPeriodStudent::getSchoolPeriodStudent($organizationId);
        if (is_numeric($inscriptions)&&$inscriptions==0){
            return self::taskError(false,false);
        }
        if (count($inscriptions)>0){
            return ($inscriptions);
        }
        return response()->json(['message'=>self::emptyInscriptions],206);
    }

    public static function getInscriptionById($id,$organizationId)
    {
        $inscription = SchoolPeriodStudent::getSchoolPeriodStudentById($id,$organizationId);
        if (is_numeric($inscription)&&$inscription==0){
            return self::taskError(false,false);
        }
        if (count($inscription)>0){
            return ($inscription)[0];
        }
        return response()->json(['message'=>self::notFoundInscription],206);
    }

    public static function getInscriptionsBySchoolPeriod($schoolPeriodId,$organizationId)
    {
        $inscriptions = SchoolPeriodStudent::getSchoolPeriodStudentBySchoolPeriod($schoolPeriodId,$organizationId);
        if (is_numeric($inscriptions)&&$inscriptions==0){
            return self::taskError(false,false);
        }
        if (count($inscriptions)>0){
            return ($inscriptions);
        }
        return response()->json(['message'=>self::emptyInscriptionInCurrentSchoolPeriod],206);
    }

    public static function getUnregisteredSubjects($student,$subjectsInSchoolPeriod)
    {
        $allSubjectsEnrolled = StudentSubject::getAllSubjectsEnrolledWithoutRET($student['id']);
        if (is_numeric($allSubjectsEnrolled) && $allSubjectsEnrolled===0){
            return 0;
        }
        $allSubjectsEnrolledId = [];
        foreach ($allSubjectsEnrolled as $subjectEnrolled){
            $allSubjectsEnrolledId[]=$subjectEnrolled['dataSubject']['subject_id'];
        }
        $equivalencesSubjectsId = array_column($student['equivalence']->toArray(),'subject_id');
        $availableSubjects=[];
        foreach ($subjectsInSchoolPeriod as $subjectInSchoolPeriod){
            if ($subjectInSchoolPeriod['enrolled_students']<$subjectInSchoolPeriod['limit']){
                if (count($allSubjectsEnrolledId)>0){
                    if (!in_array($subjectInSchoolPeriod['subject_id'],$allSubjectsEnrolledId) &&
                        !in_array($subjectInSchoolPeriod['subject_id'],$equivalencesSubjectsId)){
                        $availableSubjects[]=$subjectInSchoolPeriod;
                    }
                }else{
                    if (!in_array($subjectInSchoolPeriod['subject_id'],$equivalencesSubjectsId)){
                        $availableSubjects[]=$subjectInSchoolPeriod;
                    }
                }
            }
        }
        return $availableSubjects;
    }

    public static function filterSubjectsBySchoolProgram($schoolProgramId, $organizationId, $availableSubjects)
    {
        $subjectsInSchoolProgram = Subject::getSubjectsBySchoolProgram($schoolProgramId,$organizationId);
        if (is_numeric($subjectsInSchoolProgram)&&$subjectsInSchoolProgram===0){
            return 0;
        }
        $subjectsInSchoolProgramId = array_column( $subjectsInSchoolProgram->toArray(),'id');
        $availableSubjectsInSchoolProgram=[];
        foreach ($availableSubjects as $availableSubject){
            if (in_array($availableSubject['subject_id'],$subjectsInSchoolProgramId)){
                $availableSubjectsInSchoolProgram[]=$availableSubject;
            }
        }
        return $availableSubjectsInSchoolProgram;
    }

    public static function filterSubjectsEnrolledInSchoolPeriod($studentId,$schoolPeriodId,$availableSubjects)
    {
        $enrolledSubjects = StudentSubject::getEnrolledSubjectsBySchoolPeriodStudent($studentId,$schoolPeriodId);
        if (is_numeric($enrolledSubjects)&&$enrolledSubjects===0){
            return 0;
        }
        if (count($enrolledSubjects)>0){
            $filterSubjectsEnrolled = [];
            foreach ($availableSubjects as $availableSubject){
                $subjectFound=false;
                foreach ($enrolledSubjects as $enrolledSubject){
                    if ($availableSubject['subject_id']==$enrolledSubject['dataSubject']['subject_id']){
                        $subjectFound = true;
                        break;
                    }
                }
                if ($subjectFound ==false){
                    $filterSubjectsEnrolled[]=$availableSubject;
                }
            }
            return $filterSubjectsEnrolled;
        }
        return $availableSubjects;
    }

    public static function getTotalQualification($studentId)
    {
        $totalQualification = 0;
        $approvedSubjects = StudentSubject::getAllSubjectsEnrolledWithoutRETCUR($studentId);
        if(is_numeric($approvedSubjects)&&$approvedSubjects==0){
            return 'e'; //se coloca e porque puede existir un caso  en que este valor devolvera 0
        }
        if (count($approvedSubjects)>0){
            foreach ($approvedSubjects as $approvedSubject){
                $totalQualification += $approvedSubject['qualification'];
            }
        }
        return $totalQualification;
    }

    public static function availableProject($student,$schoolProgram, $organizationId,$dataPercentageStudent){
        $cantSchoolPeriods = SchoolPeriodStudent::getCantEnrolledSchoolPeriodByStudent($student['id'],$organizationId);
        if (is_string($cantSchoolPeriods)&&$cantSchoolPeriods==='e'){
            return 0;
        }
        if ($cantSchoolPeriods>=$schoolProgram['min_duration'] &&
            $dataPercentageStudent['enrolled_credits']+$student['credits_granted']>=
            $schoolProgram[0]['min_num_cu_final_work'] ){
            return true;
        }
        return false;
    }

    public static function getAvailableFinalSubjects($student,$schoolProgram,$organizationId,$isProject)
    {
        $availableFinalWorks = [];
        if ($isProject){ //Caso proyecto
            $finalWorksInSchoolProgram = Subject::getProjectBySchoolProgram($schoolProgram['id'],$organizationId);
        }else{// Caso trabajo final
            $finalWorksInSchoolProgram = Subject::getFinalWorkBySchoolProgram($schoolProgram['id'],$organizationId);
        }
        $approvedFinalWorks = FinalWork::getFinalWorkByStudentAndStatus($student['id'],$isProject,'APPROVED');
        if ((is_numeric($finalWorksInSchoolProgram)&&$finalWorksInSchoolProgram===0)||(is_numeric($approvedFinalWorks)&&
            $approvedFinalWorks===0)){
            return 0;
        }
        if (count($finalWorksInSchoolProgram)>0){
            if (count($approvedFinalWorks)>0){
                $approvedFinalWorksId=array_column($approvedFinalWorks->toArray(),'subject_id');
                foreach ($finalWorksInSchoolProgram as $finalWorkInSchoolProgram){
                    if (!in_array($finalWorkInSchoolProgram['id'],$approvedFinalWorksId)){
                        $availableFinalWorks[]=$finalWorkInSchoolProgram;
                    }
                }
                return $availableFinalWorks;
            }
            return $finalWorksInSchoolProgram;
        }
        return $availableFinalWorks;
    }

    public static function filterEquivalenceToStudent($equivalence,$subjectsInSchoolPeriod)
    {
        $filter = [];
        $subjectInEquivalenceId=array_column($equivalence->toArray(),'subject_id');
        foreach($subjectsInSchoolPeriod as $subject){
            if (!in_array($subject['subject_id'],$subjectInEquivalenceId)){
                $filter[]=$subject;
            }
        }
        return $filter;
    }

    public static function getAvailableSubjects($studentId,$schoolPeriodId,$organizationId,$internalCall)
    {
        $student = Student::getStudentById($studentId,$organizationId);
        if (is_numeric($student)&&$student===0){
            return self::taskError($internalCall,false);
        }
        if (count($student)>0){
            $student=$student[0];
            if ($student['current_status']!='REG' && $student['current_status']!='REI-A'
                && $student['current_status']!='REI-B' && $student['current_status']!='RIN-A'
                && $student['current_status']!='RIN-B' &&  $student['current_status']!='ENDED'){
                if ($internalCall){
                    return [];
                }
                return response()->json(['message' => self::notAllowedRegister], 206);
            }
            if ($student['end_program']==true){
                if ($internalCall){
                    return [];
                }
                return response()->json(['message' => self::endProgram], 206);
            }
            $schoolProgram=SchoolProgram::getSchoolProgramById($student['school_program_id'],$organizationId);
            if (is_numeric($schoolProgram) && $schoolProgram===0){
                return self::taskError($internalCall,false);
            }
            if (count($schoolProgram)>0){
                $schoolProgram=$schoolProgram[0];
                $thereIsUnpaidSchoolPeriod=SchoolPeriodStudent::isThereUnpaidSchoolPeriod($studentId);
                if (is_numeric($thereIsUnpaidSchoolPeriod)&&$thereIsUnpaidSchoolPeriod===0){
                    return self::taskError($internalCall,false);
                }
                if (!$thereIsUnpaidSchoolPeriod){
                    $response = [];
                    $subjectsInSchoolPeriod = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherBySchoolPeriod(
                        $schoolPeriodId);
                    if (is_numeric($subjectsInSchoolPeriod)&&$subjectsInSchoolPeriod===0){
                        return self::taskError($internalCall,false);
                    }
                    if (count($subjectsInSchoolPeriod)>0){
                        if (count($student['equivalence'])>0){
                            $subjectsInSchoolPeriod = self::filterEquivalenceToStudent($student['equivalence'],$subjectsInSchoolPeriod);
                        }
                        $unregisteredSubjects = self::getUnregisteredSubjects($student,$subjectsInSchoolPeriod);
                        if (is_numeric($unregisteredSubjects)&&$unregisteredSubjects===0){
                            return self::taskError($internalCall,false);
                        }
                        if (count($unregisteredSubjects)>0){
                            $filterSubjectsBySchoolProgram = self::filterSubjectsBySchoolProgram(
                                $student['school_program_id'], $organizationId,$unregisteredSubjects);
                            if(is_numeric($filterSubjectsBySchoolProgram)&&$filterSubjectsBySchoolProgram===0){
                                return self::taskError($internalCall,false);
                            }
                            if (count($filterSubjectsBySchoolProgram)>0){
                                $availableSubjects= self::filterSubjectsEnrolledInSchoolPeriod($studentId,$schoolPeriodId,
                                    $filterSubjectsBySchoolProgram);
                                if (is_numeric($availableSubjects)&&$availableSubjects===0){
                                    return self::taskError($internalCall,false);
                                }
                                if (count($availableSubjects)>0){
                                    $totalQualification = self::getTotalQualification($studentId);
                                    if (is_string($totalQualification)&&$totalQualification==='e'){
                                        return self::taskError($internalCall,false);
                                    }
                                    $cantSubjectsEnrolled=StudentSubject::cantAllSubjectsEnrolledWithoutRETCUR($studentId);
                                    if (is_string($cantSubjectsEnrolled)&&$cantSubjectsEnrolled==='e'){
                                        return self::taskError($internalCall,false);
                                    }
                                    if($cantSubjectsEnrolled>0 && ($totalQualification/$cantSubjectsEnrolled)<14){
                                        $response['message']=self::warningAverage;
                                    }
                                    $response['available_subjects']=$availableSubjects;
                                }
                            }
                        }
                    }
                    if (!isset($response['available_subjects'])){
                        $response['available_subjects']=[];
                    }
                    if ($schoolProgram['conducive_to_degree']){ // a partir de aqui disponibilidad de trabajo final o proyecto
                        $project = FinalWork::getFinalWorksByStudent($studentId, true);
                        if (is_numeric($project)&&$project===0){
                            return self::taskError($internalCall,false);
                        }
                        $notApprovedProject = FinalWork::existNotApprovedFinalWork($studentId, true);
                        if(is_numeric($notApprovedProject)&&$notApprovedProject===0){
                            return self::taskError($internalCall,false);
                        }
                        if ((!$notApprovedProject && count($project)>0)||$student['is_available_final_work']){
                            $availableProjectSubjects = self::getAvailableFinalSubjects($student,
                                $schoolProgram,$organizationId,false);
                            if (is_numeric($availableProjectSubjects)&& $availableProjectSubjects===0){
                                return self::taskError($internalCall,false);
                            }
                            $approvedProjects= FinalWork::getFinalWorksByStudent($studentId,true);
                            if (is_numeric($approvedProjects)&& $approvedProjects===0){
                                return self::taskError($internalCall,false);
                            }
                            if (count($availableProjectSubjects)>0){
                                $response['available_final_work']=true;
                                $response['final_work_subjects']=$availableProjectSubjects;
                            }
                            if (count($approvedProjects)>0){
                                $response['approved_projects']=$approvedProjects;
                            }
                        }
                        if(count($project)<1){
                            $notApprovedProject=true;
                        }
                        if($notApprovedProject){
                            $enrolledSubjects = SchoolPeriodStudent::getEnrolledSchoolPeriodsByStudent($student['id'],
                                $organizationId);
                            if (is_numeric($enrolledSubjects)&&$enrolledSubjects===0){
                                return self::taskError($internalCall,false);
                            }
                            if (count($enrolledSubjects)>0){
                                $dataPercentageStudent=ConstanceService::statisticsDataHistorical($enrolledSubjects);
                                $approvedDoctoralExam = DoctoralExam::existDoctoralExamApprovedByStudentInNotSchoolPeriod(
                                    $student['id'],$schoolPeriodId);
                                if (($schoolProgram['doctoral_exam']&&$approvedDoctoralExam)||
                                    !$schoolProgram['doctoral_exam']){
                                    $availableProject = self::availableProject($student,$schoolProgram,
                                        $organizationId,$dataPercentageStudent);
                                    if (is_numeric($availableProject)&&$availableProject===0){
                                        return self::taskError($internalCall,false);
                                    }
                                    if ($availableProject){
                                        $availableProjectSubjects = self::getAvailableFinalSubjects($student,
                                            $schoolProgram,$organizationId,true);
                                        if (is_numeric($availableProjectSubjects)&& $availableProjectSubjects===0){
                                            return self::taskError($internalCall,false);
                                        }
                                        if (count($availableProjectSubjects)>0){
                                            $response['available_project']=true;
                                            $response['project_subjects']=$availableProjectSubjects;
                                        }
                                    }
                                }
                                if ($schoolProgram['doctoral_exam']&&!$approvedDoctoralExam&&
                                    ($dataPercentageStudent['enrolled_credits']+$student['credits_granted']>=
                                        $schoolProgram['num_cu_to_doctoral_exam'])){
                                    $response['available_doctoral_exam']=true;
                                }
                            }

                        }
                    }
                    if (count($response['available_subjects'])<1&&(!isset($response['available_project'])&&
                            !isset($response['available_final_work']))){
                        return response()->json(['message'=>self::thereAreNotSubjectsAvailableToRegister],206);
                    }
                    return $response;
                }
                if ($internalCall){return [];}
                return response()->json(['message'=>self::thereAreSchoolPeriodWithoutPaying],206);
            }
        }
        if ($internalCall){return [];}
        return response()->json(['message'=>self::notFoundStudentGivenId],206);
    }

    public static function validate(Request $request)
    {
        $request->validate([
            'student_id'=>'required|numeric',
            'school_period_id'=>'required|numeric',
            'financing'=>'max:3|ends_with:EXO,SFI,SCS,FUN',//EXO exonerated, FUN Funded, SFI Self-financing, SCS Scholarship
            'financing_description'=>'max:60',
            'pay_ref'=>'max:50',
            'amount_paid'=>'numeric'
        ]);
    }

    public static function validateSubjects(Request $request)
    {
        $request->validate([
            'subjects.*.school_period_subject_teacher_id'=>'required|numeric',
            'subjects.*.qualification'=>'numeric',
            'subjects.*.status'=>'max:3|ends_with:CUR,RET,APR,REP'
        ]);
    }

    public static function validateProjects(Request $request)
    {
        $request->validate([
            'projects.*.title'=>'required|max:100',
            'projects.*.subject_id'=>'required|numeric',
            'projects.*.status'=>'max:10|ends_with:PROGRESS,APPROVED,REPROBATE',
            'projects.*.status_description'=>'max:200',
            'projects.*.approval_date'=>'size:10'
        ]);
    }

    public static function validateInscriptionFinalWork(Request $request)
    {
        $request->validate([
            'final_works.*.title'=>'required|max:100',
            'final_works.*.subject_id'=>'required|numeric',
            'final_works.*.project_id'=>'numeric',
            'final_works.*.status'=>'max:10|ends_with:PROGRESS,APPROVED,REPROBATE',
            'final_works.*.status_description'=>'max:200',
            'final_works.*.approval_date'=>'size:10',
            'final_works.*.advisors.*.teacher_id'=>'numeric'
        ]);
    }

    public static function validateDoctoralExam(Request $request)
    {
        $request->validate([
            'doctoral_exam.status'=>'required|max:10|ends_with:APPROVED,REPROBATE'
        ]);
    }

    public static function validateSubjectsToInscription(Request $request, $organizationId,$availableSubjects)
    {
        $existSchoolPeriodById=SchoolPeriod::existSchoolPeriodById($request['school_period_id'],$organizationId);
        if (is_numeric($existSchoolPeriodById)&&$existSchoolPeriodById===0){
            return 0;
        }
        if (!$existSchoolPeriodById){
            return false;
        }
        if (count($availableSubjects)<1){
            return false;
        }
        $availableSubjectsId=array_column($availableSubjects['available_subjects'],'id');
        foreach ($request['subjects'] as $subject){
            if (!in_array($subject['school_period_subject_teacher_id'],$availableSubjectsId)){
                return false;
            }
        }
        return true;
    }

    public static function buildStudentSubject($subject,$schoolPeriodStudentId,$isWithdrawn)
    {
        if (isset($subject['qualification'])){
            if ($subject['qualification']>=10){
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'qualification'=>$subject['qualification'],
                    'status'=>'APR'
                ];
            }else{
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'qualification'=>$subject['qualification'],
                    'status'=>'REP'
                ];
            }
        }else{
            if ($isWithdrawn){
                $studentSubject=[
                    'school_period_student_id'=>$schoolPeriodStudentId,
                    'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                    'status'=>'RET'
                ];
            }else{
                if (isset($subject['status'])){
                    $studentSubject=[
                        'school_period_student_id'=>$schoolPeriodStudentId,
                        'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                        'status'=>$subject['status']
                    ];
                }else{
                    $studentSubject=[
                        'school_period_student_id'=>$schoolPeriodStudentId,
                        'school_period_subject_teacher_id'=>$subject['school_period_subject_teacher_id'],
                        'status'=>'CUR'
                    ];
                }
            }
        }
        return $studentSubject;
    }

    public static function addSubjects($subjects,$schoolPeriodStudentId,$isWithdrawn)
    {
        foreach ($subjects as $subject){
            if (auth()->payload()['user']->user_type!='A'){
                unset($subject['qualification']);
                unset($subject['status']);
            }
            $studentSubject = self::buildStudentSubject($subject,$schoolPeriodStudentId,$isWithdrawn);
            $result = StudentSubject::addStudentSubject($studentSubject);
            if (is_numeric($result)&&$result===0){
                return 0;
            }
            $result =SchoolPeriodSubjectTeacher::updateEnrolledStudent($subject['school_period_subject_teacher_id']);
            if (is_numeric($result)&&$result===0){
                return 0;
            }
        }
    }

    public static function addAdvisors($advisors,$finalWorkId)
    {
        foreach($advisors as $advisor){
            $result = Advisor::addAdvisor(
                [
                    'final_work_id'=>$finalWorkId,
                    'teacher_id'=>$advisor['teacher_id']
                ]
            );
            if (is_numeric($result)&&$result===0){
                return 0;
            }
        }
    }

    public static function createFinalWork($studentId,$finalWork,$schoolPeriodStudentId,$isProject)
    {
        $existProject = false;
        if (!$isProject){
            $project = FinalWork::existFinalWorkByIdAndStatus($finalWork['project_id'],true,'APPROVED');
            if (is_numeric($project)&&$project===0){
                return 0;
            }
        }
        if (!$existProject){
            $finalWork['project_id']=null;
        }
        $finalWork['student_id']=$studentId;
        $finalWork['is_project']=$isProject;
        $existAdvisors= false;
        $status = 'PROGRESS';
        if (isset($finalWork['status'])){
            $status=$finalWork['status'];
            unset($finalWork['status']);
        }
        $advisors = [];
        if (isset($finalWork['advisors'])){
            $advisors=$finalWork['advisors'];
            $existAdvisors=true;
            unset($finalWork['advisors']);
        }
        $finalWorkId=FinalWork::addFinalWork($finalWork);
        if (is_numeric($finalWorkId)&&$finalWorkId===0){
            return 0;
        }
        if ($existAdvisors){
            $finalWork['advisors']=$advisors;
        }
        $finalWork['final_work_id']=$finalWorkId;
        $finalWork['school_period_student_id']=$schoolPeriodStudentId;
        $finalWork['status']=$status;
        $result = FinalWorkSchoolPeriod::addFinalWorkSchoolPeriod($finalWork);
        if (is_numeric($result)&&$result===0){
            return 0;
        }
        if (!$isProject && isset($finalWork['advisors'])){
            $result = self::addAdvisors($finalWork['advisors'],$finalWorkId);
            if (is_numeric($result)&&$result===0){
                return 0;
            }
        }
    }

    public static function existStatusREPROBATE($schoolPeriods)
    {
        foreach($schoolPeriods as $schoolPeriod){
            if ($schoolPeriod['status']=='REPROBATE'){
                return true;
            }
        }
        return false;
    }

    public static function updateFinalWork($studentId, $finalWork, $schoolPeriodStudentId, $isProject, $finalWorkInBd)
    {
        $existProject = false;
        if (!$isProject){
            $project = FinalWork::existFinalWorkByIdAndStatus($finalWork['project_id'],true,'APPROVED');
            if (is_numeric($project)&&$project===0){
                return 0;
            }
        }
        if (!$existProject){
            $finalWork['project_id']=null;
        }
        $finalWork['student_id']=$studentId;
        $finalWork['is_project']=$isProject;
        $result=FinalWork::updateFinalWork($finalWorkInBd['id'],$finalWork);
        if (is_numeric($result)&&$result===0){
            return 0;
        }
        $existFinalWorkSchoolPeriod=false;
        $finalWork['final_work_id']=$finalWorkInBd['id'];
        $finalWork['school_period_student_id']=$schoolPeriodStudentId;
        if (auth()->payload()['user']->user_type!='A' && $finalWork['status']!='PROGRESS'){
            $finalWork['status']='PROGRESS';
        }
        if (!isset($finalWork['description_status'])){
            $finalWork['description_status']=null;
        }
        foreach ($finalWorkInBd['schoolPeriods'] as $schoolPeriod){ //search for update finalWorkSchoolPeriod
            if ($schoolPeriod['finalWorkSchoolPeriod']['school_period_student_id']==$schoolPeriodStudentId){
                $result=FinalWorkSchoolPeriod::updateFinalWorkSchoolPeriod(
                    $schoolPeriod['finalWorkSchoolPeriod']['id'],$finalWork);
                $existFinalWorkSchoolPeriod=true;
                if (is_numeric($result)&&$result===0){
                    return 0;
                }
                break;
            }
        }
        if (!$existFinalWorkSchoolPeriod){
            $result = FinalWorkSchoolPeriod::addFinalWorkSchoolPeriod($finalWork);
            if (is_numeric($result)&&$result===0){
                return 0;
            }
        }
        if (!$isProject){
            $result = Advisor::deleteAllAdvisor($finalWorkInBd['id']);
            if (is_numeric($result)&&$result===0){
                return 0;
            }
            if (isset($finalWork['advisors'])){
                $result = self::addAdvisors($finalWork['advisors'],$finalWorkInBd['id']);
            }
            if (is_numeric($result)&&$result===0){
                return 0;
            }
        }
    }

    public static function setProjectsOrFinalWorks($studentId, $finalWorks, $schoolPeriodStudentId, $isProject,
                                                   $availableFinalWorks,$organizationId)
    {
        if (count($availableFinalWorks)>0){
            if (!is_array($availableFinalWorks)){
                $availableFinalWorks=$availableFinalWorks->toArray();
            }
            $availableFinalWorkIds = array_column($availableFinalWorks,'id');
            foreach ($finalWorks as $finalWork){
                $retB=false;
                if ($finalWork['status']!='APPROVED'){
                    $finalWork['approval_date']=null;
                }
                $subjectFinalWork = FinalWork::getFinalWorksByStudentSubject($studentId,$finalWork['subject_id'],
                    $isProject);
                if (is_numeric($subjectFinalWork)&&$subjectFinalWork===0){
                    return 0;
                }
                if (count($subjectFinalWork)>0){
                    $availableFinalWorkIds[]=$finalWork['subject_id'];
                }
                if (in_array($finalWork['subject_id'],$availableFinalWorkIds)){
                    $result=1;//initial value
                    if (count($subjectFinalWork)==0){ //new project or final work
                        $result = self::createFinalWork($studentId,$finalWork,$schoolPeriodStudentId,$isProject);
                    }else{//exist project or final work
                        if (count($subjectFinalWork)==1){// exist one project or final work
                            if (count($subjectFinalWork[0]['schoolPeriods'])==1){ //update first attempt or create second project or final work
                                if (self::existStatusREPROBATE($subjectFinalWork[0]['schoolPeriods'])){//second project o final work
                                    $result = self::createFinalWork($studentId,$finalWork,$schoolPeriodStudentId,
                                        $isProject);
                                    if ($finalWork['status']=="REPROBATE"){
                                        $retB=true;
                                    }
                                }else{//update first attempt
                                    $result = self::updateFinalWork($studentId,$finalWork,$schoolPeriodStudentId,
                                        $isProject,$subjectFinalWork[0]);
                                }
                            }
                        }
                        if (count($subjectFinalWork)==2) {// update second attempt
                            foreach ($subjectFinalWork as $subject){
                                if (!self::existStatusREPROBATE($subject['schoolPeriods'])){
                                    $result = self::updateFinalWork($studentId,$finalWork,$schoolPeriodStudentId,
                                        $isProject,$subject);
                                    if ($finalWork['status']=="REPROBATE"){
                                        $retB=true;
                                    }
                                }
                            }
                        }
                    }
                    if ($result===0){
                        return 0;
                    }
                    if ($retB){
                        $result = self::retBStudent($studentId,$schoolPeriodStudentId,$organizationId);
                        if ($result===0){
                            return 0;
                        }
                    }
                    if (!$isProject && isset($finalWork['status']) && $finalWork['status']==='APPROVED'){
                        $student = Student::getStudentById($studentId,$organizationId);
                        if (is_numeric($student)&&$student===0){
                            return 0;
                        }
                        $student[0]['status']='ENDED';
                        $student[0]['end_program']=true;
                        $result=Student::updateStudent($studentId,$student[0]->toArray());
                        if (is_numeric($result)&&$result==0){
                            return 0;
                        }
                    }
                }
            }
            $result = self::deleteFinalWorkSchoolPeriodNotUpdate($finalWorks,$schoolPeriodStudentId);
            if (is_numeric($result)&&$result===0){
                return 0;
            }
        }
    }

    public static function deleteFinalWorkSchoolPeriodNotUpdate($finalWorks,$schoolPeriodStudentId){
        $finalWorkSchoolPeriodsUpdated = [];
        $finalWorkSchoolPeriods = FinalWorkSchoolPeriod::getFinalWorkSchoolPeriodBySchoolPeriodStudentId($schoolPeriodStudentId);
        if (is_numeric($finalWorkSchoolPeriods)&&$finalWorkSchoolPeriods===0){
            return 0;
        }
        foreach ($finalWorks as $finalWork){
            foreach ($finalWorkSchoolPeriods as $finalWorkSchoolPeriod){
                if ($finalWorkSchoolPeriod['school_period_student_id']==$schoolPeriodStudentId &&
                    $finalWorkSchoolPeriod['finalWork']['subject_id']==$finalWork['subject_id']){
                    $finalWorkSchoolPeriodsUpdated[]= $finalWorkSchoolPeriod['id'];
                }
            }
        }
        foreach ($finalWorkSchoolPeriods as $finalWorkSchoolPeriodId){
            if (!in_array($finalWorkSchoolPeriodId['id'],$finalWorkSchoolPeriodsUpdated)){
                $finalWork = FinalWork::getFinalWork($finalWorkSchoolPeriodId['final_work_id']);
                if (is_numeric($finalWork)&&$finalWork===0){
                    return 0;
                }
                if (count($finalWork[0]['schoolPeriods'])==1){
                    $result = FinalWork::deleteFinalWork($finalWork[0]['id']);
                }else{
                    $result = FinalWorkSchoolPeriod::deleteFinalWorkSchoolPeriod($finalWorkSchoolPeriodId['id']);
                }
                if (is_numeric($result)&&$result===0){
                    return 0;
                }
            }
        }
    }

    public static function setDoctoralExam($studentId, $doctoralExam, $schoolPeriodStudentId,$organizationId){
        $retB=false;
        $doctoralExams=DoctoralExam::getDoctoralExamByStudent($studentId);
        if (is_numeric($doctoralExams)&&$doctoralExams===0){
            return 0;
        }
        $result=1;
        if (count($doctoralExams)==0){
            $result = DoctoralExam::addDoctoralExam($schoolPeriodStudentId,$doctoralExam['status']);
        }
        if (count($doctoralExams)==1){
            if ($doctoralExams[0]['school_period_student_id']==$schoolPeriodStudentId){
                $delete=DoctoralExam::deleteDoctoralExam($schoolPeriodStudentId);
                if (is_numeric($delete)&&$delete===0){
                    return 0;
                }
                $result = DoctoralExam::addDoctoralExam($schoolPeriodStudentId,$doctoralExam['status']);
            }else{
                $result = DoctoralExam::addDoctoralExam($schoolPeriodStudentId,$doctoralExam['status']);
                if ($doctoralExam['status']=='REPROBATE'){
                    $retB=true;
                }
            }
        }else{
        }
        if (count($doctoralExams)==2){
            foreach ($doctoralExams as $exam){
                if ($exam['school_period_student_id']==$schoolPeriodStudentId){
                    $delete=DoctoralExam::deleteDoctoralExam($schoolPeriodStudentId);
                    if (is_numeric($delete)&&$delete===0){
                        return 0;
                    }
                    $result = DoctoralExam::addDoctoralExam($schoolPeriodStudentId,$doctoralExam['status']);
                    if ($doctoralExam['status']=='REPROBATE'){
                        $retB=true;
                    }
                }
            }
        }
        if (is_numeric($result)&&$result==0){
            return 0;
        }
        if ($retB){
            $result = self::retBStudent($studentId,$schoolPeriodStudentId,$organizationId);
            if (is_numeric($result)&&$result==0){
                return 0;
            }
        }
    }

    public static function retBStudent($studentId,$schoolPeriodStudentId,$organizationId){
        $schoolPeriodStudent=SchoolPeriodStudent::getSchoolPeriodStudentById($schoolPeriodStudentId,$organizationId);
        if (is_numeric($schoolPeriodStudent)&&$schoolPeriodStudent===0){
            return 0;
        }
        $schoolPeriodStudent[0]['status']='RET-B';
        unset($schoolPeriodStudent[0]['student']);
        unset($schoolPeriodStudent[0]['enrolledSubjects']);
        unset($schoolPeriodStudent[0]['schoolPeriod']);
        unset($schoolPeriodStudent[0]['finalWorkData']);
        $result=SchoolPeriodStudent::updateSchoolPeriodStudentLikeArray($schoolPeriodStudentId,
            $schoolPeriodStudent[0]->toArray());
        if (is_numeric($result)&&$result==0){
            return 0;
        }
        $student = Student::getStudentById($studentId,$organizationId);
        if (is_numeric($student)&&$student===0){
            return 0;
        }
        $student[0]['status']='RET-B';
        $student[0]['end_program']=true;
        $result=Student::updateStudent($studentId,$student[0]->toArray());
        if (is_numeric($result)&&$result==0){
            return 0;
        }
    }

    public static function addInscription(Request $request,$organizationId)
    {
        self::validate($request);
        if (isset($request['subjects'])&&count($request['subjects'])>0){
            self::validateSubjects($request);
        }
        if (isset($request['projects'])){
            self::validateProjects($request);
        }
        if (isset($request['final_works'])){
            self::validateInscriptionFinalWork($request);
        }
        if (isset($request['doctoral_exam'])){
            self::validateDoctoralExam($request);
        }
        if (isset($request['projects'])||isset($request['final_works'])||isset($request['subjects'])){
            $existSchoolPeriod=SchoolPeriodStudent::existSchoolPeriodStudent($request['student_id'],
                $request['school_period_id']);
            if (is_numeric($existSchoolPeriod)&&$existSchoolPeriod===0){
                return self::taskError(false,false);
            }
            if (!$existSchoolPeriod) {
                $student=Student::getStudentById($request['student_id'],$organizationId);
                if (is_numeric($student)&&$student===0){
                    return self::taskError(false,false);
                }
                if (count($student)>0){
                    $request['status']=$student[0]['current_status'];
                    $request['test_period']=$student[0]['test_period'];
                    if ($request['status']!='RET-A'&&$request['status']!='RET-B'&&$request['status']!='DES-A'&&
                        $request['status']!='DES-B'&&$request['status']!='ENDED'){
                        $availableSubjects = self::getAvailableSubjects($request['student_id'],
                            $request['school_period_id'], $organizationId, true);
                        if (is_numeric($availableSubjects)&&$availableSubjects===0){
                            return self::taskError(false,false);
                        }
                        $schoolPeriodStudentId=SchoolPeriodStudent::addSchoolPeriodStudent($request);
                        if (is_numeric($schoolPeriodStudentId)&&$schoolPeriodStudentId===0){
                            return self::taskError(false,false);
                        }
                        if (isset($request['subjects'])&&count($request['subjects'])>0){
                            $validateSubjectToInscription=self::validateSubjectsToInscription($request,$organizationId,
                                $availableSubjects);
                            if (is_numeric($validateSubjectToInscription)&&$validateSubjectToInscription===0){
                                return self::taskError(false,false);
                            }
                            if($validateSubjectToInscription){
                                $result = self::addSubjects($request['subjects'],$schoolPeriodStudentId,false);
                                if (is_numeric($result)&&$result===0){
                                    return self::taskError(false,true);
                                }
                            }else{
                                $result = SchoolPeriodStudent::deleteSchoolPeriodStudent($schoolPeriodStudentId);
                                if (is_numeric($result)&&$result===0){
                                    return self::taskError(false,true);
                                }
                                return response()->json(['message'=>self::invalidSubjectToInscription],206);
                            }
                        }
                        $result=1; //flag para el resultado generico
                        if ((isset($availableSubjects['available_doctoral_exam'])&&
                                $availableSubjects['available_doctoral_exam']) && isset($request['doctoral_exam'])){
                            $result =self::setDoctoralExam($student[0]['id'],$request['doctoral_exam'],
                                $schoolPeriodStudentId,$organizationId);
                        }
                        if ((isset($availableSubjects['available_project'])&&$availableSubjects['available_project'])
                            && isset($request['projects'])){
                            $result =self::setProjectsOrFinalWorks($student[0]['id'],$request['projects'],
                                $schoolPeriodStudentId, true, $availableSubjects['project_subjects'],
                                $organizationId);
                        }
                        if ((isset($availableSubjects['available_final_work'])&&$availableSubjects['available_final_work'])
                            && isset($request['final_works'])){
                            $result = self::setProjectsOrFinalWorks($student[0]['id'],$request['final_works'],
                                $schoolPeriodStudentId,false, $availableSubjects['final_work_subjects'],
                                $organizationId);
                        }
                        if (is_numeric($result)&&$result===0){
                            return self::taskError(false,true);
                        }
                        $log = Log::addLog(auth('api')->user()['id'],
                            self::logAddInscription.$request['student_id']);
                        if (is_numeric($log)&&$log==0){
                            return self::taskError(false,true);
                        }
                        return self::getInscriptionById($schoolPeriodStudentId,$organizationId);
                    }
                    return response()->json(['message' => self::notAllowedRegister], 206);
                }
                return response()->json(['message'=>self::notFoundStudentGivenId],206);
            }
            return response()->json(['message'=>self::inscriptionReady],206);
        }
        return response()->json(['message' => self::notRegister], 206);
    }

    public static function deleteInscription($id,$organizationId)
    {
        $schoolPeriodStudentById=SchoolPeriodStudent::getSchoolPeriodStudentById($id,$organizationId);
        if (is_numeric($schoolPeriodStudentById) && $schoolPeriodStudentById===0){
            return self::taskError(false,false);
        }
        if(count($schoolPeriodStudentById)>0){
            $result=SchoolPeriodStudent::deleteSchoolPeriodStudent($id);
            if (is_numeric($result)&&$result===0){
                return self::taskError(false,false);
            }
            $log = Log::addLog(auth('api')->user()['id'],
                self::logDeleteFinalWork.$schoolPeriodStudentById[0]['student_id']);
            if (is_numeric($log)&&$log==0){
                return self::taskError(false,true);
            }
            return response()->json(['message'=>self::OK]);
        }
        return response()->json(['message'=>self::notFoundInscription],206);
    }

    public static function validateRelationUpdate(Request $request,$organizationId,$availableSubjects)
    {
        $existSchoolPeriodById=SchoolPeriod::existSchoolPeriodById($request['school_period_id'],$organizationId);
        if (is_numeric($existSchoolPeriodById)&&$existSchoolPeriodById===0){
            return 0;
        }
        if (!$existSchoolPeriodById){
            return false;
        }
        $subjectsEnrolledInSchoolPeriod = SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],
            $request['school_period_id']);
        if (is_numeric($subjectsEnrolledInSchoolPeriod)&&$subjectsEnrolledInSchoolPeriod===0){
            return 0;
        }
        $subjectsEnrolledInSchoolPeriod=$subjectsEnrolledInSchoolPeriod[0]['enrolledSubjects'];
        if (count($availableSubjects)<1 && count($subjectsEnrolledInSchoolPeriod)<1){
            return false;
        }
        $availableSubjectsId=array_column($availableSubjects['available_subjects'],'id');
        foreach ($subjectsEnrolledInSchoolPeriod as $subjectEnrolledInSchoolPeriod){
            if (!in_array($subjectEnrolledInSchoolPeriod['school_period_subject_teacher_id'],$availableSubjectsId)){
                $availableSubjectsId[]=$subjectEnrolledInSchoolPeriod['school_period_subject_teacher_id'];
            }
        }
        foreach ($request['subjects'] as $subject){
            if (!in_array($subject['school_period_subject_teacher_id'],$availableSubjectsId)){
                return false;
            }
        }
        return true;
    }

    public static function updateStatusDESA($schoolPeriodStudentId, $organizationId) //Actualiza el status del estudiante sobre el periodo escolar
    {
        $schoolPeriodStudent = SchoolPeriodStudent::getSchoolPeriodStudentById($schoolPeriodStudentId,$organizationId);
        if (is_numeric($schoolPeriodStudent)&&$schoolPeriodStudent===0){
            return 0;
        }
        $schoolPeriodStudent = $schoolPeriodStudent[0];
        $enrolledSubjects = $schoolPeriodStudent['enrolledSubjects'];
        $allWithdrawn=true;
        foreach ($enrolledSubjects as $enrolledSubject){
            if ($enrolledSubject['status']!='RET'){
                $allWithdrawn = false;
                break;
            }
        }
        if ($allWithdrawn){
            $schoolPeriodStudent['status']='DES-A'; //Si un estudiante retira todas las materias debe caer en DES-A
            $result =SchoolPeriodStudent::updateSchoolPeriodStudentLikeArray($schoolPeriodStudentId,
                $schoolPeriodStudent->toArray());
            if (is_numeric($result)&&$result===0){
                return 0;
            }
            $student = Student::getStudentById($schoolPeriodStudent['student_id'],$organizationId);
            if (is_numeric($student)&&$student===0){
                return 0;
            }
            $student[0]['current_status']='DES-A';
            $result = Student::updateStudent($schoolPeriodStudent['student_id'],$student[0]->toArray());
            if (is_numeric($result)&&$result===0){
                return 0;
            }
        }
    }

    public static function updateSubjects($subjects,$schoolPeriodStudentId,$organizationId,$isWithdrawn)
    {
        $subjectsInBd=StudentSubject::studentSubjectBySchoolPeriodStudent($schoolPeriodStudentId);
        if (is_numeric($subjectsInBd)&&$subjectsInBd===0){
            return 0;
        }
        $subjectsUpdated=[];
        foreach ($subjects as $subject){
            $existSubject = false;
            foreach ($subjectsInBd as $subjectInBd){
                if ($subject['school_period_subject_teacher_id']==$subjectInBd['school_period_subject_teacher_id']){
                    $studentSubject = self::buildStudentSubject($subject,$schoolPeriodStudentId,$isWithdrawn);
                    $result = StudentSubject::updateStudentSubject($subjectInBd['id'],$studentSubject);
                    if (is_numeric($result)&&$result===0){
                        return 0;
                    }
                    $subjectsUpdated[]=$subjectInBd['id'];
                    $existSubject=true;
                    break;
                }
            }
            if ($existSubject==false){
                $result=self::addSubjects([$subject],$schoolPeriodStudentId,$isWithdrawn);
                if (is_numeric($result)&&$result===0){
                    return 0;
                }
                $schoolPeriodStudentIdAdd=StudentSubject::findStudentSubjectId($schoolPeriodStudentId,
                    $subject['school_period_subject_teacher_id']);
                if (is_numeric($schoolPeriodStudentIdAdd)&&$schoolPeriodStudentIdAdd){
                    return 0;
                }
                if (count($schoolPeriodStudentIdAdd)>0){
                    $subjectsUpdated[]=$schoolPeriodStudentIdAdd[0]['id'];
                }
            }
        }
        foreach ($subjectsInBd as $subjectId){
            if (!in_array($subjectId['id'],$subjectsUpdated)){
                $deleteStudentSubject=StudentSubject::deleteStudentSubject($subjectId['id']);
                if (is_numeric($deleteStudentSubject)&&$deleteStudentSubject===0){
                    return 0;
                }
            }
        }
        $result = self::updateStatusDESA($schoolPeriodStudentId,$organizationId);
        if (is_numeric($result)&&$result===0){
            return 0;
        }
    }

    public static function updateInscription(Request $request, $id,$organizationId)
    {
        self::validate($request);
        if (isset($request['subjects'])&&count($request['subjects'])>0){
            self::validateSubjects($request);
        }
        if (isset($request['projects'])){
            self::validateProjects($request);
        }
        if (isset($request['final_works'])){
            self::validateInscriptionFinalWork($request);
        }
        if (isset($request['doctoral_exam'])){
            self::validateDoctoralExam($request);
        }
        if (isset($request['projects'])||isset($request['final_works'])||isset($request['subjects'])){
            $schoolPeriodStudentInBd= SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],
                $request['school_period_id']);
            if (is_numeric($schoolPeriodStudentInBd)&&$schoolPeriodStudentInBd===0){
                return self::taskError(false,false);
            }
            if(count($schoolPeriodStudentInBd)>0 && $schoolPeriodStudentInBd[0]['id']==$id){
                $student=Student::getStudentById($request['student_id'],$organizationId);
                if (is_numeric($student)&&$student===0){
                    return self::taskError(false,false);
                }
                if (count($student)>0){
                    $request['status']=$student[0]['current_status'];
                    $request['test_period']=$student[0]['test_period'];
                    if($request['status']!='RET-A'&&$request['status']!='RET-B'&&$request['status']!='DES-A'&&
                        $request['status']!='DES-B'&&$request['status']!='ENDED'){
                        $availableSubjects = self::getAvailableSubjects($request['student_id'],$request['school_period_id'],
                            $organizationId, true);
                        if (is_numeric($availableSubjects)&&$availableSubjects===0){
                            return self::taskError(false,false);
                        }
                        $result = SchoolPeriodStudent::updateSchoolPeriodStudent($id,$request);
                        if (is_numeric($result)&&$result===0){
                            return self::taskError(false,false);
                        }
                        if (isset($request['subjects'])&&count($request['subjects'])>0){
                            $validateRelationUpdate=self::validateRelationUpdate($request,$organizationId,
                                $availableSubjects);
                            if (is_numeric($validateRelationUpdate)&&$validateRelationUpdate===0){
                                return self::taskError(false,false);
                            }
                            if($validateRelationUpdate){
                                $result = self::updateSubjects($request['subjects'],$id,$organizationId,false);
                                if (is_numeric($result)&&$result===0){
                                    return self::taskError(false,true);
                                }
                            }else{
                                return response()->json(['message'=>self::invalidSubjectToInscription],206);
                            }
                        }else{
                            $result=StudentSubject::deleteStudentSubjectBySchoolPeriodStudentId($id);
                            if (is_numeric($result)&&$result===0){
                                return self::taskError(false,true);
                            }
                        }
                        if ((isset($availableSubjects['available_project'])&&$availableSubjects['available_project']) &&
                            isset($request['projects'])){
                            $result =self::setProjectsOrFinalWorks($student[0]['id'],$request['projects'],
                                $id, true, $availableSubjects['project_subjects'],$organizationId);
                        }
                        if ((isset($availableSubjects['available_final_work'])&&
                            $availableSubjects['available_final_work']) && isset($request['final_works'])){
                            $result = self::setProjectsOrFinalWorks($student[0]['id'],$request['final_works'],
                                $id,false, $availableSubjects['final_work_subjects'],$organizationId);
                        }
                        if ((isset($availableSubjects['available_project']) && ($availableSubjects['available_project'])
                                && !isset($request['projects']) && (isset($availableSubjects['available_final_work']) &&
                                    $availableSubjects['available_final_work']) && !isset($request['final_works']))){
                            $result=FinalWorkSchoolPeriod::existFinalWorkSchoolPeriodBySchoolPeriodStudent($id);
                            if (is_numeric($result)&&$result===0){
                                return self::taskError(false,true);
                            }
                            $result=FinalWorkSchoolPeriod::deleteFinalWorkSchoolPeriodBySchoolPeriodStudentId($id);
                        }
                        if (is_numeric($result)&&$result===0){
                            return self::taskError(false,true);
                        }
                        if ((isset($availableSubjects['available_doctoral_exam'])&&
                                $availableSubjects['available_doctoral_exam']) && isset($request['doctoral_exam'])){
                            $result =self::setDoctoralExam($student[0]['id'],$request['doctoral_exam'], $id,
                                $organizationId);
                        }else{
                            $result= DoctoralExam::deleteDoctoralExam($id);
                        }
                        if (is_numeric($result)&&$result===0){
                            return self::taskError(false,true);
                        }
                        $log = Log::addLog(auth('api')->user()['id'],
                            self::logUpdateInscription.$request['student_id']);
                        if (is_numeric($log)&&$log==0){
                            return self::taskError(false,true);
                        }
                        return self::getInscriptionById($id,$organizationId);
                    }
                    return response()->json(['message' => self::notAllowedRegister], 206);
                }
                return response()->json(['message'=>self::notFoundStudentGivenId],206);
            }
            return response()->json(['message'=>self::notFoundInscription],206);
        }
        return response()->json(['message' => self::notRegister], 206);
    }

    public static function studentAvailableSubjects($studentId,$organizationId)
    {
        $isValid=StudentService::validateStudent($organizationId,$studentId);
        if ($isValid==='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if(is_numeric($currentSchoolPeriod)&&$currentSchoolPeriod===0){
                return self::taskError(false,false);
            }
            if (count($currentSchoolPeriod)>0){
                if ($currentSchoolPeriod[0]['inscription_visible']==true){
                    return self::getAvailableSubjects($studentId,$currentSchoolPeriod[0]['id'],$organizationId,false);
                }
                return response()->json(['message'=>self::notAvailableInscriptions],206);
            }
            return response()->json(['message'=>self::noCurrentSchoolPeriod],206);
        }
        return $isValid;
    }

    public static function studentAddInscription(Request $request,$organizationId)
    {
        $isValid=StudentService::validateStudent($organizationId,$request['student_id']);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (is_numeric($currentSchoolPeriod)&&$currentSchoolPeriod===0){
                 return self::taskError(false,false);
            }
            if (count($currentSchoolPeriod)>0){
                if ($currentSchoolPeriod[0]['inscription_visible']==true){
                    $request['school_period_id']=$currentSchoolPeriod[0]['id'];
                    unset($request['pay_ref']);
                    unset($request['amount_paid']);
                    if (isset($request['subjects'])){
                        $subjects = $request['subjects'];
                        for ($i=0;count($request['subjects'])>$i;$i++){
                            $subject = $subjects[$i];
                            unset($subject['qualification']);
                            unset($subject['status']);
                            $subjects[$i]=$subject;
                        }
                        $request['subjects']=$subjects;
                    }
                    if (isset($request['projects'])){
                        $projects =$request['projects'];
                        for ($i=0;$i<count($projects);$i++){
                            $project = $projects[$i];
                            unset($project['status']);
                            unset($project['description_status']);
                            unset($project['approval_date']);
                            $projects[$i]=$project;
                        }
                        $request['projects']=$projects;
                    }
                    if (isset($request['final_works'])){
                        $finalWorks= $request['final_works'];
                        for ($i=0;$i<count($finalWorks);$i++){
                            $finalWork = $finalWorks[$i];
                            unset($finalWork['status']);
                            unset($finalWork['description_status']);
                            unset($finalWork['approval_date']);
                            $finalWorks[$i] = $finalWork;
                        }
                        $request['final_works']=$finalWorks;
                    }
                    unset($request['doctoral_exam']);
                    return self::addInscription($request,$organizationId);
                }
                return response()->json(['message'=>self::notAvailableInscriptions],206);
            }
            return response()->json(['message'=>self::noCurrentSchoolPeriod],206);
        }
        return $isValid;
    }

    public static function getCurrentEnrolledSubjects($studentId,$organizationId){
        $isValid=StudentService::validateStudent($organizationId,$studentId);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (is_numeric($currentSchoolPeriod)&&$currentSchoolPeriod===0){
                return response()->json(['message'=>self::taskError],206);
            }
            if (count($currentSchoolPeriod)>0){
                $inscription = SchoolPeriodStudent::findSchoolPeriodStudent($studentId,$currentSchoolPeriod[0]['id']);
                $finalWork = FinalWork::getFinalWorkByStudentAndStatus($studentId,false,'PROGRESS');
                $project = FinalWork::getFinalWorkByStudentAndStatus($studentId,true,'PROGRESS');
                if (is_numeric($inscription)&&$inscription===0){
                    return self::taskError(false,false);
                }
                if (count($inscription)>0){
                    if (count($finalWork)>0){
                        $inscription[0]['final_work']=$finalWork;
                    }
                    if (count($project)>0){
                        $inscription[0]['project']=$project;
                    }
                    return $inscription[0];
                }
                return response()->json(['message'=>self::emptyInscriptions],206);
            }
            return response()->json(['message'=>self::noCurrentSchoolPeriod],206);
        }
        return $isValid;
    }

    public static function validateWithdrawSubjectsBody(Request $request)
    {
        $request->validate([
            'student_id'=>'required|numeric',
            'withdraw_subjects.*.student_subject_id'=>'required|numeric'
        ]);
    }

    public static function validateWithdrawSubjects($withdrawSubjects,$enrolledSubjects)
    {
        $enrolledSubjectsId = array_column($enrolledSubjects->toArray(),'id');
        foreach ($withdrawSubjects as $withdrawSubject){
            if (!in_array($withdrawSubject['student_subject_id'],$enrolledSubjectsId)){
                return false;
            }
            return true;
        }
    }

    public static function changeStatusSubjectsToRET($schoolPeriodStudentId,$organizationId,$withdrawSubjects)
    {
        foreach ($withdrawSubjects as $withdrawSubject){
            $studentSubject = StudentSubject::getStudentSubjectById($withdrawSubject['student_subject_id']);
            if (is_numeric($studentSubject)&&$studentSubject===0){
                return 0;
            }
            $studentSubject = $studentSubject[0];
            $studentSubject['status']='RET';
            $studentSubject=StudentSubject::updateStudentSubject($withdrawSubject['student_subject_id'],
                $studentSubject->toArray());
            if (is_numeric($studentSubject)&&$studentSubject===0){
                return 0;
            }
        }
        $result=self::updateStatusDESA($schoolPeriodStudentId,$organizationId);
        if (is_numeric($result)&&$result==0){
            return 0;
        }
    }

    public static function withdrawSubjects(Request $request,$organizationId)
    {
        self::validateWithdrawSubjectsBody($request);
        $isValid=StudentService::validateStudent($organizationId,$request['student_id']);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (is_numeric($currentSchoolPeriod)&&$currentSchoolPeriod==0){
                return self::taskError(false,false);
            }
            if (count($currentSchoolPeriod)>0){
                if (strtotime($currentSchoolPeriod[0]['withdrawal_deadline'])>=strtotime(now()->toDateTimeString())){
                    $inscription = SchoolPeriodStudent::findSchoolPeriodStudent($request['student_id'],
                        $currentSchoolPeriod[0]['id']);
                    if (is_numeric($inscription)&&$inscription===0){
                        return self::taskError(false,false);;
                    }
                    if (count($inscription)>0){
                        if (self::validateWithdrawSubjects($request['withdraw_subjects'],
                            $inscription[0]['enrolledSubjects'])){
                            $result =self::changeStatusSubjectsToRET($inscription[0]['id'],$organizationId,
                                $request['withdraw_subjects']);
                            if (is_numeric($result)&&$result==0){
                                return self::taskError(false,false);
                            }
                            $log = Log::addLog(auth('api')->user()['id'],
                                self::logWithdrawSubject.$request['student_id']);
                            if (is_numeric($log)&&$log==0){
                                return self::taskError(false,true);
                            }
                            return response()->json(['message'=>self::OK],200);
                        }
                        return response()->json(['message'=>self::invalidSubject],206);
                    }
                    return response()->json(['message'=>self::notCurrentInscription],206);
                }
                return response()->json(['message'=>self::expiredDate],206);
            }
            return response()->json(['message'=>self::noCurrentSchoolPeriod],206);
        }
        return $isValid;
    }

    public static function getEnrolledStudentsInSchoolPeriod($teacherId,$schoolPeriodSubjectTeacherId,$organizationId)
    {
        $isValid=TeacherService::validateTeacher($teacherId,$organizationId);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if (is_numeric($currentSchoolPeriod)&&$currentSchoolPeriod==0){
                return self::taskError(false,false);
            }
            if (count($currentSchoolPeriod)>0){
                $schoolPeriodSubjectTeacher= SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherById(
                    $schoolPeriodSubjectTeacherId);
                if (is_numeric($schoolPeriodSubjectTeacher)&&$schoolPeriodSubjectTeacher===0){
                    return self::taskError(false,false);
                }
                if ($schoolPeriodSubjectTeacher[0]['teacher_id']==$teacherId &&
                    $schoolPeriodSubjectTeacher[0]['school_period_id']==$currentSchoolPeriod[0]['id'] ){
                    $enrolledStudents=StudentSubject::studentSubjectBySchoolPeriodSubjectTeacherId(
                        $schoolPeriodSubjectTeacherId);
                    if (is_numeric($enrolledStudents)&&$enrolledStudents===0){
                        return self::taskError(false,false);
                    }
                    if (count($enrolledStudents)>0){
                        return $enrolledStudents;
                    }
                    return response()->json(['message'=>self::emptyInscriptions],206);
                }
                return response()->json(['message'=>self::invalidSubject],206);
            }
            return response()->json(['message'=>self::noCurrentSchoolPeriod],206);
        }
        return $isValid;
    }

    public static function validateLoadNotes(Request $request)
    {
        $request->validate([
            'teacher_id'=>'required|numeric',
            'school_period_subject_teacher_id'=>'required|numeric',
            'student_notes.*.student_subject_id'=>'required|numeric',
            'student_notes.*.qualification'=>'required|numeric'
        ]);
    }

    public static function validateNotes(Request $request, $schoolPeriodId)
    {
        $teacherId=$request['teacher_id'];
        $schoolPeriodSubjectTeacherId=$request['school_period_subject_teacher_id'];
        $studentNotes=$request['student_notes'];
        $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::getSchoolPeriodSubjectTeacherById($schoolPeriodSubjectTeacherId);
        if(is_numeric($schoolPeriodSubjectTeacher)&&$schoolPeriodSubjectTeacher==0){
            return 0;
        }
        if (count($schoolPeriodSubjectTeacher)<1){
            return false;
        }
        if ($schoolPeriodSubjectTeacher[0]['teacher_id']!=$teacherId ||
            $schoolPeriodSubjectTeacher[0]['school_period_id']!=$schoolPeriodId){
            return false;
        }
        $enrolledStudents=StudentSubject::studentSubjectBySchoolPeriodSubjectTeacherId($schoolPeriodSubjectTeacherId);
        if (is_numeric($enrolledStudents)&&$enrolledStudents===0){
            return 0;
        }
        if (count($enrolledStudents)<1){
            return false;
        }
        foreach ($studentNotes as $studentNote){
            $found = false;
            foreach ($enrolledStudents as $enrolledStudent){
                if($enrolledStudent['id']==$studentNote['student_subject_id'] && $enrolledStudent['status']!='RET' &&
                    $studentNote['qualification']>=0 && $studentNote['qualification']<=20){
                    $found = true;
                    break;
                }
            }
            if ($found == false){
                return false;
            }
        }
        return true;
    }

    public static function changeNotes($studentNotes){
        foreach($studentNotes as $studentNote){
            $studentSubject= StudentSubject::getStudentSubjectById($studentNote['student_subject_id']);
            if (is_numeric($studentSubject)&&$studentSubject===0){
                return 0;
            }
            $studentSubject[0]['qualification']=$studentNote['qualification'];
            $studentSubjectPrepare = self::buildStudentSubject($studentSubject[0],
                $studentSubject[0]['school_period_student_id'],false);
            $result=StudentSubject::updateStudentSubject($studentSubject[0]['id'],$studentSubjectPrepare);
            if (is_numeric($result)&&$result===0){
                return 0;
            }

        }
    }

    public static function loadNotes(Request $request,$organizationId)
    {
        self::validateLoadNotes($request);
        $isValid=TeacherService::validateTeacher($request['teacher_id'],$organizationId);
        if ($isValid=='valid'){
            $currentSchoolPeriod= SchoolPeriod::getCurrentSchoolPeriod($organizationId);
            if(is_numeric($currentSchoolPeriod)&&$currentSchoolPeriod===0){
                return self::taskError(false,false);
            }
            if (count($currentSchoolPeriod)>0){
                $validNotes = self::validateNotes($request,$currentSchoolPeriod[0]['id']);
                if (is_numeric($validNotes)&&$validNotes===0){
                    return self::taskError(false,false);
                }
                if ($validNotes && $currentSchoolPeriod[0]['load_notes']==true){
                    $schoolPeriodsStudentForUpdate=self::changeNotes($request['student_notes']);
                    if (is_numeric($schoolPeriodsStudentForUpdate)&&$schoolPeriodsStudentForUpdate===0){
                        return self::taskError(false,false);
                    }
                    $log = Log::addLog(auth('api')->user()['id'],
                        self::logLoadNotes);
                    if (is_numeric($log)&&$log==0){
                        return self::taskError(false,true);
                    }
                    return response()->json(['message'=>self::OK],200);
                }
                return response()->json(['message'=>self::invalidData],206);
            }
            return response()->json(['message'=>self::noCurrentSchoolPeriod],206);
        }
        return $isValid;
    }

    public static function deleteFinalWork($id){
        $finalWork = FinalWork::getFinalWork($id);
        if (is_numeric($finalWork)&&$finalWork===0){
            return self::taskError(false,false);
        }
        if (count($finalWork)>0){
            $result = FinalWork::deleteFinalWork($id);
            if (is_numeric($result)&&$result===0){
                return self::taskError;
            }
            $log = Log::addLog(auth('api')->user()['id'],
                self::logDeleteFinalWork.$finalWork[0]['student_id']);
            if (is_numeric($log)&&$log==0){
                return self::taskError(false,true);
            }
            return response()->json(['message'=>self::OK]);
        }
        return response()->json(['message'=>self::notFoundFinalWork]);
    }

}
