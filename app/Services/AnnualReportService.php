<?php
/**
 * Created by PhpStorm.
 * User: hector
 * Date: 25/07/20
 * Time: 05:10 PM
 */

namespace App\Services;
use App\Exports\AnnualReport;
use App\SchoolPeriod;
use App\SchoolProgram;
use App\Student;
use App\Subject;
use App\User;
use Carbon\Carbon;
use Excel;

class AnnualReportService
{
    const taskError = 'No se puede proceder con la tarea';
    const schoolPeriodsValid = 'Debe proporcionar periodos escolares validos';
    const dateSchoolPeriods = 'Debe proveer los periodos escolares de manera ascendente con respecto a las fechas';

    public static function exportAnnualReport($firstSchoolPeriodId,$lastSchoolPeriodId,$organizationId)
    {
        $firstSchoolPeriod = SchoolPeriod::getSchoolPeriodById($firstSchoolPeriodId,$organizationId);
        $lastSchoolPeriod=SchoolPeriod::getSchoolPeriodById($lastSchoolPeriodId,$organizationId);
        if ((is_numeric($firstSchoolPeriod)&&$firstSchoolPeriod===0) ||
            (is_numeric($lastSchoolPeriod)&&$lastSchoolPeriod==0)){
            return response()->json(['message' => self::taskError], 206);
        }
        if (count($firstSchoolPeriod)<1 || count($lastSchoolPeriod)<1){
            return response()->json(['message' => self::schoolPeriodsValid], 206);
        }
        if ($firstSchoolPeriod[0]['start_date']>$lastSchoolPeriod[0]['start_date']){
            return response()->json(['message' => self::dateSchoolPeriods], 206);
        }
        $schoolPeriods = SchoolPeriod::getSchoolPeriods($organizationId);
        if (is_numeric($schoolPeriods)&&$schoolPeriods===0){
            return response()->json(['message' => self::taskError], 206);
        }
        $schoolPeriodsToReport = [];
        foreach ($schoolPeriods->toArray() as $schoolPeriod){
            if ($schoolPeriod['start_date']>=$firstSchoolPeriod[0]['start_date'] && $schoolPeriod['start_date']<=
                $lastSchoolPeriod[0]['start_date']){
                $schoolPeriodsToReport[]=$schoolPeriod;
            }
        }
        $annualReport = new AnnualReport($schoolPeriodsToReport,$organizationId);
        return Excel::download($annualReport,'InformeAnual.xlsx');
    }

    //EnrolledStudents
    public static function cmpSchoolProgramId($a, $b)
    {
        return strcmp($a["school_program_id"], $b["school_program_id"]);
    }

    public static function cmpSchoolPeriodStartDate($a, $b)
    {
        return strcmp($a["start_date"], $b["start_date"]);
    }

    public static function filterStudents($students,$schoolPeriods)
    {
        $firstSchoolPeriod = $schoolPeriods[0];
        $lastSchoolPeriod = end($schoolPeriods);
        $filterStudents=[];
        foreach ($students as $student){
            if (count($student['school_period'])>0){
                $studentFirst=self::getFirstSchoolPeriod($student['school_period'],false);
                $studentLast=self::getLastSchoolPeriod($student['school_period'],false);
                if ($studentFirst['school_period']['start_date']<=$lastSchoolPeriod['start_date'] &&
                    ($student['current_status']!='RET-B'&&$student['current_status']!='ENDED')){
                    $filterStudents[]=$student;
                }
                if ($studentLast['school_period']['start_date']>=$firstSchoolPeriod['start_date'] &&
                    ($student['current_status']=='RET-B'||$student['current_status']=='ENDED')){
                    $filterStudents[]=$student;
                }
            }
        }
        return $filterStudents;
    }

    public static function getFirstSchoolPeriod($schoolPeriods,$code)
    {
        if (count($schoolPeriods)>0){
            $minorDate = $schoolPeriods[0];
            foreach ($schoolPeriods as $schoolPeriod){
                if ($minorDate['school_period']['start_date']>$schoolPeriod['school_period']['start_date']){
                    $minorDate=$schoolPeriod;
                }
            }
            if ($code){
                return $minorDate['school_period']['cod_school_period'];
            }
            return $minorDate;
        }
        return '';
    }

    public static function getLastSchoolPeriod($schoolPeriods,$code)
    {
        if (count($schoolPeriods)>0){
            $majorDate = $schoolPeriods[0];
            foreach ($schoolPeriods as $schoolPeriod){
                if ($majorDate['school_period']['start_date']<$schoolPeriod['school_period']['start_date']){
                    $majorDate=$schoolPeriod;
                }
            }
            if ($code){
                return $majorDate['school_period']['cod_school_period'];
            }
            return $majorDate;
        }
        return '';
    }

    public static function getDataEnrolledSchoolPeriods($schoolPeriods,$schoolPeriodsEnrolled)
    {
        $credits = [];
        $schoolPeriodsId = array_column($schoolPeriods,'id');
        if (count($schoolPeriodsEnrolled)>0){
            foreach ($schoolPeriodsId as $schoolPeriodId){
                $amountCreditsEnrolled = 0;
                foreach ($schoolPeriodsEnrolled as $schoolPeriod){
                    if ($schoolPeriodId == $schoolPeriod['school_period_id']){
                        foreach ($schoolPeriod['enrolled_subjects'] as $enrolledSubject){
                            $amountCreditsEnrolled = $amountCreditsEnrolled + $enrolledSubject['data_subject']['subject']['uc'];
                        }
                        break;
                    }
                }
                $credits[]=$amountCreditsEnrolled;
            }
            foreach ($schoolPeriodsId as $schoolPeriodId){
                $amountCreditsEnrolled = 0;
                $amountCreditsWithdrawn = 0;
                foreach ($schoolPeriodsEnrolled as $schoolPeriod){
                    if ($schoolPeriodId == $schoolPeriod['school_period_id']){
                        foreach ($schoolPeriod['enrolled_subjects'] as $enrolledSubject){
                            $amountCreditsEnrolled = $amountCreditsEnrolled + $enrolledSubject['data_subject']['subject']['uc'];
                            if ($enrolledSubject['status']=='REP'){
                                $amountCreditsWithdrawn = $amountCreditsWithdrawn + $enrolledSubject['data_subject']['subject']['uc'];
                            }
                        }
                        break;
                    }
                }
                $credits[]=$amountCreditsWithdrawn==$amountCreditsEnrolled&&$amountCreditsEnrolled!=0?'RETIRO TOTAL':$amountCreditsWithdrawn;
            }
            foreach ($schoolPeriodsId as $schoolPeriodId){
                $amountCreditsApproved = 0;
                foreach ($schoolPeriodsEnrolled as $schoolPeriod){
                    if ($schoolPeriodId == $schoolPeriod['school_period_id']){
                        foreach ($schoolPeriod['enrolled_subjects'] as $enrolledSubject){
                            if ($enrolledSubject['status']=='REP'){
                                $amountCreditsApproved = $amountCreditsApproved + $enrolledSubject['data_subject']['subject']['uc'];
                            }
                        }
                        break;
                    }
                }
                $credits[]=$amountCreditsApproved;
            }
        }else{
            foreach ($schoolPeriodsId as $schoolPeriodId){
                $credits[]=0;
                $credits[]=0;
                $credits[]=0;
            }
        }
        return $credits;
    }

    public static function existFinalWork($schoolPeriods,$schoolPeriodsEnrolled)
    {
        $schoolPeriodsId = array_column($schoolPeriods,'id');
        foreach ($schoolPeriodsEnrolled as $schoolPeriodEnrolled){
            if (count($schoolPeriodEnrolled['final_work_data'])>0){
                if (in_array($schoolPeriodEnrolled['school_period_id'],$schoolPeriodsId)){
                    foreach ($schoolPeriodEnrolled['final_work_data'] as $finalWork){
                        if (!$finalWork['final_work']['is_project']){
                            return 'SI';
                        }
                    }
                }
            }
        }
        Return 'NO';
    }

    public static function amountCanceled($schoolPeriods,$schoolPeriodsEnrolled)
    {
        $amountCanceled = 0;
        $schoolPeriodsId = array_column($schoolPeriods,'id');
        foreach ($schoolPeriodsEnrolled as $schoolPeriod){
            if (in_array($schoolPeriod['school_period_id'],$schoolPeriodsId)){
                $amountCanceled = $amountCanceled + $schoolPeriod['amount_paid'];
            }
        }
        return $amountCanceled;
    }

    public static function setBodyByRowEnrolledStudents($schoolPrograms, $schoolPeriods, $students)
    {
        $body = [];
        foreach ($students as $student){
            $row =[];
            foreach ($schoolPrograms as $schoolProgram){
                if ($student['school_program_id']==$schoolProgram['id']){
                    $row[]=$schoolProgram['school_program_name'];
                    $row[]=$student['user']['first_surname'].' '.$student['user']['second_surname'];
                    $row[]=$student['user']['first_name'].' '.$student['user']['second_name'];
                    $row[]=$student['user']['identification'];
                    $row[]=$student['user']['nationality'];
                    $row[]=$student['user']['sex'];
                    $row[]=self::getFirstSchoolPeriod($student['school_period'],true);
                    $row[]=self::getLastSchoolPeriod($student['school_period'],true);
                    $schoolPeriodsEnrolled=self::getDataEnrolledSchoolPeriods($schoolPeriods,$student['school_period']);
                    $row = array_merge($row,$schoolPeriodsEnrolled);
                    $row[]=self::existFinalWork($schoolPeriods,$student['school_period']);
                    $row[]=self::amountCanceled($schoolPeriods,$student['school_period']);
                    $row[]='NO';
                    $row[]='';
                    $row[]=$student['user']['with_disabilities']?'SI':'NO';
                    $row[]=$student['is_ucv_teacher']?'SI':'NO';
                    $row[]='NO';
                    break;
                }
            }
            $body[]=$row;
        }
        return $body;
    }

    public static function getEnrolledStudents($schoolPeriods,$organizationId){
        $sheetEnrolledStudents = [];
        usort($schoolPeriods,'self::cmpSchoolPeriodStartDate');
        $title ='ESTUDIANTES INSCRITOS EN LOS SEMESTRES ';
        foreach ($schoolPeriods as $schoolPeriod){
            $title=$title.$schoolPeriod['cod_school_period'].' ';
        }
        $header= [
            [$title],
            []
        ];
        $subHeader = [
            'PROGRAMA',
            'APELLIDOS',
            'NOMBRES',
            'CEDULA O PASAPORTE',
            'NACIONALIDAD',
            'SEXO',
            'FECHA DE INGRESO (SEMESTRE)',
            'ULTIMO SEMESTRE INSCRITO',
        ];
        foreach ($schoolPeriods as $schoolPeriod){
            $subHeader[]='NÚMERO DE CRÉDITOS INSCRITOS '.$schoolPeriod['cod_school_period'];
        }
        foreach ($schoolPeriods as $schoolPeriod){
            $subHeader[]='NÚMERO DE CRÉDITOS RETIRADOS '.$schoolPeriod['cod_school_period'];
        }
        foreach ($schoolPeriods as $schoolPeriod){
            $subHeader[]='NÚMERO DE CRÉDITOS APROBADOS '.$schoolPeriod['cod_school_period'];
        }
        $subHeader=array_merge($subHeader, ['INSCRIPCION DE TESIS',
            'MONTO CANCELADO',
            'FINACIMIENTO',
            'DEPENDENCIA DE LA U.C.V. ó INSTITUCION  QUE FINANCIA',
            'DISCAPASITADO',
            'PROFESOR DE LA U.C.V.',
            'BECA C.D.C.H.']);
        $header[]=$subHeader;
        $sheetEnrolledStudents[]=$header;
        $schoolPrograms=SchoolProgram::getSchoolProgram($organizationId);
        $schoolProgramsFilter = array_filter($schoolPrograms->toArray(), function ($obj){
            if ($obj['conducive_to_degree']){
                return true;
            }
            return false;
        });
        $students=Student::getAllStudent($organizationId);
        $students=$students->toArray();
        usort($students,'self::cmpSchoolProgramId');
        $students=self::filterStudents($students,$schoolPeriods);
        $body=self::setBodyByRowEnrolledStudents($schoolProgramsFilter,$schoolPeriods,$students);
        $sheetEnrolledStudents[]=$body;
        return $sheetEnrolledStudents;
    }

    //IrregularStudent
    public static function setBodyRowIrregularStudents($schoolPeriods,$students,$schoolPrograms){
        $last = end($schoolPeriods);
        $body = [];
        foreach ($students as $student){
            $row = [];
            foreach ($schoolPrograms as $schoolProgram){
                if ($student['school_program_id']==$schoolProgram['id']){
                    foreach ($student['school_period'] as $schoolPeriod){
                        if ($schoolPeriod['school_period_id']==$last['id']){
                            if ($schoolPeriod['status']=='DES-A'||$schoolPeriod['status']=='DES-B'||
                                $schoolPeriod['status']=='RET-A'||$schoolPeriod['status']=='RET-B'||
                                $schoolPeriod['status']=='RIN-A'||$schoolPeriod['status']=='RIN-B'||
                                $schoolPeriod['status']=='REI-A'||$schoolPeriod['status']=='REI-B'){
                                $row[]=$schoolProgram['school_program_name'];
                                $row[]=$student['user']['first_surname'].' '.$student['user']['second_surname'];
                                $row[]=$student['user']['first_name'].' '.$student['user']['second_name'];
                                $row[]=$student['user']['identification'];
                                $row[]=$student['user']['nationality'];
                                $row[]=$student['user']['sex'];
                                if ($schoolPeriod['status']=='DES-A'||$schoolPeriod['status']=='DES-B'){
                                    $row[]=$schoolPeriod['status']=='DES-A'?'TIPO A':'TIPO B';
                                }else{
                                    $row[]='';
                                }
                                if ($schoolPeriod['status']=='REI-A'||$schoolPeriod['status']=='REI-B'){
                                    $row[]=$schoolPeriod['status']=='REI-A'?'TIPO A':'TIPO B';
                                }else{
                                    $row[]='';
                                }
                                if ($schoolPeriod['status']=='RET-A'||$schoolPeriod['status']=='RET-B'){
                                    $row[]=$schoolPeriod['status']=='RET-A'?'TIPO A':'TIPO B';
                                }else{
                                    $row[]='';
                                }
                                if ($schoolPeriod['status']=='RIN-A'||$schoolPeriod['status']=='RIN-B'){
                                    $row[]=$schoolPeriod['status']=='RIN-A'?'TIPO A':'TIPO B';
                                }else{
                                    $row[]='';
                                }
                            }
                        }
                    }
                }
            }
            $body[]=$row;
        }
        return $body;
    }

    public static function getIrregularStudents($schoolPeriods,$organizationId){
        $sheetIrregularStudents = [];
        usort($schoolPeriods,'self::cmpSchoolPeriodStartDate');
        $title ='ESTUDIANTES DESINCORPORACIONES, REINCORPORACIONES, RETIROS Y REINGRESOS EN LOS SEMESTRES ';
        foreach ($schoolPeriods as $schoolPeriod){
            $title=$title.$schoolPeriod['cod_school_period'].' ';
        }
        $header= [
            [$title],
            []
        ];
        $subHeader = [
            'PROGRAMA',
            'APELLIDOS',
            'NOMBRES',
            'CEDULA O PASAPORTE',
            'NACIONALIDAD',
            'SEXO',
            'TIPO DE DESINCORPORACIÓN',
            'TIPO DE REINCORPORACIÓN',
            'TIPO DE RETIRO',
            'TIPO DE REINGRESO',
            ];
        $header[]=$subHeader;
        $sheetIrregularStudents[]=$header;
        $students=Student::getAllStudent($organizationId);
        $students=$students->toArray();
        usort($students,'self::cmpSchoolProgramId');
        $students=self::filterStudents($students,$schoolPeriods);
        $schoolPrograms=SchoolProgram::getSchoolProgram($organizationId);
        $schoolProgramsFilter = array_filter($schoolPrograms->toArray(), function ($obj){
            if ($obj['conducive_to_degree']){
                return true;
            }
            return false;
        });
        $body=self::setBodyRowIrregularStudents($schoolPeriods,$students,$schoolProgramsFilter);
        $sheetIrregularStudents[]=$body;
        return $sheetIrregularStudents;
    }

    //NotConduciveToDegree
    public static function filterSchoolPeriodsBySubjects($schoolPeriods,$subjects)
    {
        $subjectsId = array_column($subjects->toArray(),'id');
        $schoolPeriodsFilter= [];
        foreach ($schoolPeriods as $schoolPeriod){
            foreach ($schoolPeriod['subjects'] as $subject){
                if (in_array($subject['subject_id'],$subjectsId)){
                    $schoolPeriodsFilter[]=$schoolPeriod;
                    break;
                }
            }
        }
        return $schoolPeriodsFilter;
    }

    public static function isSubjectAssociatedWithSchoolProgram($schoolProgramId,$subjects,$subjectId)
    {
        foreach ($subjects as $subject){
            if ($subject['id']==$subjectId){
                foreach ($subject['schoolPrograms'] as $schoolProgram){
                    if ($schoolProgram['id']==$schoolProgramId){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function getRowCounters($subjectId,$schoolPeriodId,$students,$schoolProgramId)
    {
        $counters=[];
        $counters['cur_mas']=0;
        $counters['cur_fem']=0;
        $counters['cul_mas']=0;
        $counters['cul_fem']=0;
        $counters['aba_mas']=0;
        $counters['aba_fem']=0;
        $counters['total']=0;
        foreach ($students as $student){
            if (count($student['school_period'])>0 && $student['school_program_id']==$schoolProgramId){
                foreach ($student['school_period'] as $schoolPeriod){
                    if ($schoolPeriod['school_period_id']==$schoolPeriodId && count($schoolPeriod['enrolled_subjects'])>0){
                        foreach ($schoolPeriod['enrolled_subjects'] as $subject){
                            if ($subject['data_subject']['subject_id']==$subjectId){
                                if ($student['user']['sex']=='M'){
                                    $counters['cur_mas']++;
                                    if ($subject['status']=='APR' || $subject['status']=='REP'){
                                        $counters['cul_mas']++;
                                    }else{
                                        $counters['aba_mas']++;
                                    }
                                }else{
                                    $counters['cur_fem']++;
                                    if ($subject['status']=='APR' || $subject['status']=='REP'){
                                        $counters['cul_fem']++;
                                    }else{
                                        $counters['aba_fem']++;
                                    }
                                }
                                $counters['total']++;
                            }
                        }
                    }
                }
            }
        }
        return $counters;
    }

    public static function setBodyRowNotConduciveToDegree($schoolPeriods,$students,$schoolPrograms,$subjects)
    {
        $body=[];
        $body[]=[''];
        $i=0;
        $totalCounters=[];
        $totalCounters['cur_mas']=0;
        $totalCounters['cur_fem']=0;
        $totalCounters['cul_mas']=0;
        $totalCounters['cul_fem']=0;
        $totalCounters['aba_mas']=0;
        $totalCounters['aba_fem']=0;
        $totalCounters['pro_mas']=0;
        $totalCounters['pro_fem']=0;
        $totalCounters['total']=0;
        $totalCounters['costo']=0;
        foreach ($schoolPrograms as $schoolProgram){
            $body[]=['Total '.$schoolProgram['school_program_name']];
            foreach ($schoolPeriods as $schoolPeriod){
                foreach ($schoolPeriod['subjects'] as $subject){
                    $row = [];
                    if (self::isSubjectAssociatedWithSchoolProgram($schoolProgram['id'],$subjects,$subject['subject_id'])){
                        $row[]=$subject['subject']['name'];
                        for ($j=0;$j<$i;$j++){
                            $row[]='';
                        }
                        $row[]='X';
                        for ($j=$i+1;$j<count($schoolPrograms);$j++){
                            $row[]='';
                        }
                        $rowCounters=self::getRowCounters($subject['subject_id'],$schoolPeriod['id'],$students,$schoolProgram['id']);
                        $row[]=$rowCounters['cur_mas'];
                        $totalCounters['cur_mas']=$totalCounters['cur_mas'] + $rowCounters['cur_mas'];
                        $row[]=$rowCounters['cur_fem'];
                        $totalCounters['cur_fem']=$totalCounters['cur_fem'] + $rowCounters['cur_fem'];
                        $row[]=$rowCounters['cul_mas'];
                        $totalCounters['cul_mas']= $totalCounters['cul_mas'] +$rowCounters['cul_mas'];
                        $row[]=$rowCounters['cul_fem'];
                        $totalCounters['cul_fem']=$totalCounters['cul_fem']+$rowCounters['cul_fem'];
                        $row[]=$rowCounters['aba_mas'];
                        $totalCounters['aba_fem']= $totalCounters['aba_fem']+$rowCounters['aba_mas'];
                        $row[]=$rowCounters['aba_fem'];
                        $totalCounters['aba_fem']=$totalCounters['aba_fem']+$rowCounters['aba_fem'];
                        if($subject['teacher']['user']['sex']=='M'){
                            $row[]=1;
                            $row[]=0;
                            $totalCounters['pro_mas']++;
                        }else{
                            $row[]=0;
                            $row[]=1;
                            $totalCounters['pro_fem']++;
                        }
                        $row[]=$rowCounters['total'];
                        $totalCounters['total']=$totalCounters['total']+$rowCounters['total'];
                        $row[]=$subject['duty'];
                        $totalCounters['costo']=$totalCounters['costo']+$subject['duty'];
                    }
                    $body[]=$row;
                }
            }
            $body[]=[''];
            $i++;
        }
        $finalRow=['Total Cursos no conducente a grado académico'];
        for ($j=0;$j<count($schoolPrograms);$j++){
            $finalRow[]='';
        }
        $finalRow[]=$totalCounters['cur_mas'];
        $finalRow[]=$totalCounters['cur_fem'];
        $finalRow[]=$totalCounters['cul_mas'];
        $finalRow[]=$totalCounters['cul_fem'];
        $finalRow[]=$totalCounters['aba_mas'];
        $finalRow[]=$totalCounters['aba_fem'];
        $finalRow[]=$totalCounters['pro_mas'];
        $finalRow[]=$totalCounters['pro_fem'];
        $finalRow[]=$totalCounters['total'];
        $finalRow[]=$totalCounters['costo'];
        $body[]=$finalRow;
        return $body;
    }

    public static function getNotConduciveToDegree($schoolPeriods,$organizationId)
    {
        $sheetNotConduciveToDegree = [];
        $schoolPrograms=SchoolProgram::getSchoolProgram($organizationId);
        $schoolProgramsFilter = array_filter($schoolPrograms->toArray(), function ($obj){
            if (!$obj['conducive_to_degree']){
                return true;
            }
            return false;
        });
        usort($schoolPeriods,'self::cmpSchoolPeriodStartDate');
        $header= [
            []
        ];
        $subHeaderFirst = [
            'Denominación de curso'
        ];
        foreach ($schoolProgramsFilter as $schoolProgram){
            $subHeaderFirst[]=$schoolProgram['school_program_name'];
        }
        $subHeaderLast = [
            'Cursantes Mas.',
            'Cursantes Fem.',
            'Culminaron Mas.',
            'Culminaron Fem.',
            'Abandonaron Mas.',
            'Abandonaron Fem.',
            'N° de Profesores Mas.',
            'N° de Profesores Fem.',
            'TOTAL',
            'Costo'
        ];
        $header[]=array_merge($subHeaderFirst,$subHeaderLast);
        $sheetNotConduciveToDegree[]=$header;
        $students=Student::getAllStudentToNotDegree($organizationId);
        $students=$students->toArray();
        $subjects= Subject::getSubjectsInProgramsNotDegree($organizationId);
        $schoolPeriodsFilter=self::filterSchoolPeriodsBySubjects($schoolPeriods,$subjects);
        $body=self::setBodyRowNotConduciveToDegree($schoolPeriodsFilter,$students,$schoolProgramsFilter,$subjects);
        $sheetNotConduciveToDegree[]=$body;
        return $sheetNotConduciveToDegree;
    }

    //IrregularFinalWorks
    public static function enrolledFinalWork($schoolPeriods, $studentSchoolPeriods, $isProject)
    {
        $schoolPeriodsId=array_column($schoolPeriods,'id');
        foreach ($studentSchoolPeriods as $schoolPeriod){
            if (in_array($schoolPeriod['school_period_id'],$schoolPeriodsId)&&count($schoolPeriod['final_work_data'])>0){
                foreach ($schoolPeriod['final_work_data'] as $finalWork){
                    if ($finalWork['final_work']['is_project']==$isProject){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public static function getDateApprovedProject($studentSchoolPeriods)
    {
        $projectId = 0;
        foreach ($studentSchoolPeriods as $schoolPeriod){
            foreach ($schoolPeriod['final_work_data'] as $finalWork){
                if ($finalWork['final_work']['is_project']==false){
                    $projectId=$finalWork['final_work']['project_id'];
                    break;
                }
            }
        }
        foreach ($studentSchoolPeriods as $schoolPeriod){
            foreach ($schoolPeriod['final_work_data'] as $finalWork){
                if ($finalWork['final_work_id']==$projectId){
                    return $finalWork['final_work']['approval_date'];
                }
            }
        }
        return '';
    }

    public static function getDescriptionStatusProject($studentSchoolPeriods)
    {
        $description = '';
        foreach ($studentSchoolPeriods as $schoolPeriod){
            foreach ($schoolPeriod['final_work_data'] as $finalWork){
                if ($finalWork['final_work']['is_project']==true && $finalWork['description_status']!=null){
                    $description = $finalWork['description_status'];
                }
            }
        }
        return $description;
    }

    public static function counterCreditsInStudent($student,$schoolPeriods)
    {
        $schoolPeriodEnd=end($schoolPeriods);
        $equivalences = 0;
        if (count($student['equivalence'])){
            foreach ($student['equivalence'] as $equivalence){
                $equivalences = $equivalences + $equivalence['subject']['uc'];
            }
        }
        $creditsInSchoolPeriods=0;
        if (count($student['school_period'])>0){
            foreach ($student['school_period'] as $schoolPeriod){
                if (count($schoolPeriod['enrolled_subjects'])>0 &&
                    $schoolPeriod['school_period']['start_date']<=$schoolPeriodEnd['start_date']){
                    foreach ($schoolPeriod['enrolled_subjects'] as $subject){
                        if ($subject['status']=='APR'){
                            $creditsInSchoolPeriods=$creditsInSchoolPeriods + $subject['data_subject']['subject']['uc'];
                        }
                    }
                }
            }
        }
        return $student['credits_granted']+$equivalences+$creditsInSchoolPeriods;
    }

    public static function setBodyByRowIrregularFinalWorks($schoolPrograms, $schoolPeriods, $students)
    {
        $body = [];
        $now = Carbon::now();
        foreach ($students as $student){
            $row =[];
            foreach ($schoolPrograms as $schoolProgram){
                if ($student['school_program_id']==$schoolProgram['id']){
                    $firstSchoolPeriod=self::getFirstSchoolPeriod($student['school_period'],false);
                    $day=intval(substr($firstSchoolPeriod['school_period']['start_date'],8));
                    $month=intval(substr($firstSchoolPeriod['school_period']['start_date'],5,3));
                    $year=intval(substr($firstSchoolPeriod['school_period']['start_date'],0,4))+
                        $schoolProgram['duration'];
                    $isEnrolledProject = self::enrolledFinalWork($schoolPeriods,$student['school_period'],true);
                    $isEnrolledFinalWork = self::enrolledFinalWork($schoolPeriods,$student['school_period'],false);
                    if (($year<$now->year || ($year==$now->year && $month<$now->month) ||
                        ($year==$now->year && $month==$now->month && $day<$now->day))&&($isEnrolledFinalWork||
                            $isEnrolledProject)){
                        $row[]=$schoolProgram['school_program_name'];
                        $row[]=$student['user']['first_surname'].' '.$student['user']['second_surname'].' '.
                            $student['user']['first_name'].' '.$student['user']['second_name'];
                        $row[]=$student['user']['identification'];
                        $row[]=$student['user']['sex'];
                        if ($isEnrolledFinalWork){
                            $row[]=self::getDateApprovedProject($student['school_period']);
                        }
                        if (!$isEnrolledFinalWork && $isEnrolledProject){
                            $row[]=self::getDescriptionStatusProject($student['school_period']);
                        }
                        $row[]='';
                        break;
                    }
                }
            }
            $body[]=$row;
        }
        $body[]=['ESTUDIANTES QUE NO HAN PRESENTADO PROYECTO DE TRABAJO DE GRADO ó TESIS DOCTORAL TENIENDO MÁS DE 24 CRÉDITOS APROBADOS '];
        $body[]=[];
        $body[]=['PROGRAMA','APELLIDOS Y NOMBRES','CEDULA DE IDENTIDAD','SEXO','NUMERO DE CREDITOS APROBADOS'];
        foreach ($schoolPrograms as $schoolProgram){
            $row=[];
            foreach ($students as $student){
                if ($student['school_program_id']==$schoolProgram['id']){
                    $isEnrolledProject = self::enrolledFinalWork($schoolPeriods,$student['school_period'],true);
                    $isEnrolledFinalWork = self::enrolledFinalWork($schoolPeriods,$student['school_period'],false);
                    if (!$isEnrolledProject&&!$isEnrolledFinalWork){
                        $credits= self::counterCreditsInStudent($student,$schoolPeriods);
                        if ($credits>24){
                            $row[]=$schoolProgram['school_program_name'];
                            $row[]=$student['user']['first_surname'].' '.$student['user']['second_surname'].' '.
                                $student['user']['first_name'].' '.$student['user']['second_name'];
                            $row[]=$student['user']['identification'];
                            $row[]=$student['user']['sex'];
                            $row[]=$credits;
                        }
                    }
                }
            }
            $body[]=$row;
        }
        return $body;
    }

    public static function getIrregularFinalWorks($schoolPeriods,$organizationId){
        $sheetIrregularFinalWorks = [];
        usort($schoolPeriods,'self::cmpSchoolPeriodStartDate');
        $title ='ESTUDIANTES CON INSCRIPCIONES DE TESIS DOCTORAL ó TRABAJOS DE GRADO EXCEDIENDO EL LAPSO DE CULMINACIÓN PREVISTO EN REGLAMENTO';
        $header= [
            [$title],
            []
        ];
        $subHeader = [
            'PROGRAMA',
            'APELLIDOS Y NOMBRE',
            'CEDULA DE IDENTIDAD',
            'SEXO',
            'FECHA DE APROBACIÓN DE PROYECTO',
            'REGLAMENTO QUE RIGE AL ESTUDIANTE',
        ];

        $header[]=$subHeader;
        $sheetIrregularFinalWorks[]=$header;
        $schoolPrograms=SchoolProgram::getSchoolProgram($organizationId);
        $schoolProgramsFilter = array_filter($schoolPrograms->toArray(), function ($obj){
            if ($obj['conducive_to_degree']){
                return true;
            }
            return false;
        });
        $students=Student::getAllStudent($organizationId);
        $students=$students->toArray();
        usort($students,'self::cmpSchoolProgramId');
        $students=self::filterStudents($students,$schoolPeriods);
        $body=self::setBodyByRowIrregularFinalWorks($schoolProgramsFilter,$schoolPeriods,$students);
        $sheetIrregularFinalWorks[]=$body;
        return $sheetIrregularFinalWorks;
    }

    //ApprovedFinalWorks
    public static function getApprovedFinalWork($studentSchoolPeriods,$schoolPeriods)
    {
        $schoolPeriodsId=array_column($schoolPeriods,'id');
        foreach ($studentSchoolPeriods as $schoolPeriod){
            if (in_array($schoolPeriod['school_period_id'],$schoolPeriodsId)&&count($schoolPeriod['final_work_data'])>0){
                foreach ($schoolPeriod['final_work_data'] as $finalWork){
                    if ($finalWork['final_work']['is_project']==false && $finalWork['status']=='APPROVED'){
                        return $finalWork;
                    }
                }
            }
        }
        return [];
    }

    public static function setBodyRowApprovedFinalWorks($schoolPrograms, $schoolPeriods, $students){
        $body = [];
        foreach ($students as $student){
            $row =[];
            foreach ($schoolPrograms as $schoolProgram){
                if ($student['school_program_id']==$schoolProgram['id'] && count($student['school_period'])){
                    $finalWork=self::getApprovedFinalWork($student['school_period'],$schoolPeriods);
                    if (count($finalWork)>0){
                        $row[]=$schoolProgram['school_program_name'];
                        $row[]=$finalWork['final_work']['title'];
                        $row[]=$student['user']['first_surname'].' '.$student['user']['second_surname'].' '.
                            $student['user']['first_name'].' '.$student['user']['second_name'];
                        $tutorNames = '';
                        if (count($finalWork['final_work']['teachers'])>0){
                            foreach ($finalWork['final_work']['teachers'] as $advisor){
                                $tutorNames = $tutorNames.$advisor['user']['first_surname'].' '.
                                    $advisor['user']['second_surname'].' '. $advisor['user']['first_name'].' '.
                                    $advisor['user']['second_name'].', ';
                            }
                            $tutorNames=substr($tutorNames,0,-2);
                        }
                        $row[]=$tutorNames;
                        $date='';
                        if ($finalWork['status']=='APPROVED'){
                            $date=$finalWork['final_work']['approval_date'];
                            $date=strtoupper(ConstanceService::numberToMonth(substr($date,5,3))).' '.
                                substr($date,0,4);
                            /*if ($finalWork['description_status']!=null){
                                $date=strtoupper(ConstanceService::numberToMonth(substr($date,5,3))).' '.
                                    substr($date,0,4)
                                    .', '.$finalWork['description_status'];
                            }*/
                        }/*else{
                            if ($finalWork['description_status']!=null){
                                $date=$finalWork['description_status'];
                            }
                        }*/
                        $row[]=$date;
                    }
                    break;
                }
            }
            $body[]=$row;
        }
        return $body;
    }

    public static function getApprovedFinalWorks($schoolPeriods,$organizationId)
    {
        $sheetApprovedFinalWorks = [];
        usort($schoolPeriods,'self::cmpSchoolPeriodStartDate');
        $title ='TESIS DOCTORALES, TRABAJOS DE GRADO Y TRABAJOS DE ESPECIALIZACIÓN  EN LOS SEMESTRES ';
        foreach ($schoolPeriods as $schoolPeriod){
            $title=$title.$schoolPeriod['cod_school_period'].' ';
        }
        $header= [
            [$title],
            []
        ];
        $subHeader = [
            'NIVEL | PROGRAMA',
            'TITULO',
            'NOMBRE DEL AUTOR',
            'NOMBRE DEL TUTOR ó TUTORES ',
            'FECHA DE APROBACIÓN DEL VEREDICTO DE GRADO '
        ];
        $header[]=$subHeader;
        $sheetApprovedFinalWorks[]=$header;
        $schoolPrograms=SchoolProgram::getSchoolProgram($organizationId);
        $schoolProgramsFilter = array_filter($schoolPrograms->toArray(), function ($obj){
            if ($obj['conducive_to_degree']){
                return true;
            }
            return false;
        });
        $students=Student::getAllStudent($organizationId);
        $students=$students->toArray();
        usort($students,'self::cmpSchoolProgramId');
        $students=self::filterStudents($students,$schoolPeriods);
        $body=self::setBodyRowApprovedFinalWorks($schoolProgramsFilter,$schoolPeriods,$students);
        $sheetApprovedFinalWorks[]=$body;
        return $sheetApprovedFinalWorks;
    }

    //TeachingGroup
    public  static function getTeachersWithoutINV($teachers){
        $filterTeachers=[];
        foreach ($teachers as $teacher){
            if ($teacher['teacher']['category']!='INV'){
                $filterTeachers[]=$teacher;
            }
        }
        return $filterTeachers;
    }

    public static function setBodyRowTeachingGroup($teachers, $schoolPeriods)
    {
        $body = [];
        foreach ($teachers as $teacher){
            $schoolPeriodsTeaching = '';
            foreach ($schoolPeriods as $schoolPeriod){
                if (count($schoolPeriod['subjects'])>0){
                    foreach ($schoolPeriod['subjects'] as $subject ){
                        if ($subject['teacher_id'] == $teacher['id']){
                            $schoolPeriodsTeaching=$schoolPeriodsTeaching.$schoolPeriod['cod_school_period'].'/';
                        }
                    }
                }
            }
            if ($schoolPeriodsTeaching!=''){
                $row=[];
                $row[]=$teacher['first_surname'].' '.$teacher['second_surname'];
                $row[]=$teacher['first_name'].' '.$teacher['second_name'];
                $row[]=$teacher['identification'];
                switch ($teacher['teacher']['teacher_type']){
                    case 'CON':
                        $row[]=$teacher['sex']=='M'?'Contratado':'Contratada';
                        break;
                    case 'JUB':
                        $row[]=$teacher['sex']=='M'?'Jubilado':'Jubilada';
                        break;
                    case 'REG':
                        $row[]='Regular';
                        break;
                    default:
                        $row[]='Otro';
                }
                $row[]=$teacher['sex'];
                switch ($teacher['level_instruction']){
                    case 'TSU':
                        $row[]='Técnico Superior Universitario';
                        break;
                    case 'TCM':
                        $row[]='Técnico medio';
                        break;
                    case 'Dr':
                        $row[]='Doctorado';
                        break;
                    case 'Esp':
                        $row[]='Especialista';
                        break;
                    case 'Ing':
                        $row[]=$teacher['sex']=='M'?'Ingeniero':'Ingeniera';
                        break;
                    case 'MSc':
                        $row[]='Magister Scientiarum';
                        break;
                    default:
                        $row[]=$teacher['sex']=='M'?'Licenciado':'Licenciada';
                }
                switch ($teacher['teacher']['category']){
                    case 'INS':
                        $row[]='Instructor';
                        break;
                    case 'ASI':
                        $row[]='Asistente';
                        break;
                    case 'AGR':
                        $row[]='Agregado';
                        break;
                    case 'ASO':
                        $row[]='Asociado';
                        break;
                    case 'TIT':
                        $row[]='Titular';
                        break;
                    default:
                        $row[]='';
                }
                switch ($teacher['teacher']['dedication']){
                    case 'MT':
                        $row[]='Medio Tiempo';
                        break;
                    case 'TC':
                        $row[]='Tiempo Convencional';
                        break;
                    case 'EXC':
                        $row[]='Exclusiva';
                        break;
                    case 'TCO':
                        $row[]='Tiempo Completo';
                        break;
                    default:
                        $row[]='';
                }
                $row[]=substr($schoolPeriodsTeaching,0,-1);
                $body[]=$row;
            }
        }
        return $body;
    }

    public static function getTeachingGroup($schoolPeriods,$organizationId)
    {
        $sheetTeachingGroup = [];
        usort($schoolPeriods,'self::cmpSchoolPeriodStartDate');
        $title ='PLANTA PROFESORAL QUE DICTARON CLASE EN LOS SEMESTRES ';
        foreach ($schoolPeriods as $schoolPeriod){
            $title=$title.$schoolPeriod['cod_school_period'].' ';
        }
        $header= [
            [$title],
            []
        ];
        $subHeader = [
            'APELLIDOS',
            'NOMBRES',
            'CEDULA O PASAPORTE',
            'TIPO DE PROFESOR',
            'SEXO',
            'GRADO ACADÉMICO',
            'CATEGORIA',
            'TIEMPO DE DEDICACIÓN',
            'SEMESTRE EN QUE DICTARON CLASES',
            'PERTENECE AL PROGRAMA DE ESTIMULO A LA INVESTIG. (PEI)',
            'DEPENDENCIA DE LA U.C.V. ó DE OTRA INSTITUCIÓN A LA QUE PERTENECEN'
        ];
        $header[]=$subHeader;
        $sheetTeachingGroup[]=$header;
        $teachers = UserService::getUsers('T',$organizationId);
        $filterTeachers=self::getTeachersWithoutINV($teachers->toArray());
        $body=self::setBodyRowTeachingGroup($filterTeachers,$schoolPeriods);
        $sheetTeachingGroup[]=$body;
        return $sheetTeachingGroup;
    }

    //TeachingGroup
    public  static function getTeachersINV($teachers){
        $filterTeachers=[];
        foreach ($teachers as $teacher){
            if ($teacher['teacher']['category']=='INV'){
                $filterTeachers[]=$teacher;
            }
        }
        return $filterTeachers;
    }

    public static function setBodyRowGuestTeachers($teachers, $schoolPeriods)
    {
        $body = [];
        foreach ($teachers as $teacher){
            $datesTeaching = '';
            $subjectsTeaching ='';
            foreach ($schoolPeriods as $schoolPeriod){
                if (count($schoolPeriod['subjects'])>0){
                    foreach ($schoolPeriod['subjects'] as $subject ){
                        if ($subject['teacher_id'] == $teacher['id']){
                            $subjectsTeaching=$subjectsTeaching.$subject['subject']['name']. ', ';
                            if ($subject['start_date']!=null){
                                $datesTeaching= $datesTeaching. substr($subject['start_date'],8).' de '.
                                    ConstanceService::numberToMonth(substr($subject['start_date'],5,3))
                                    .' de '.substr($subject['start_date'],0,4).' / '.
                                    substr($subject['end_date'],8).' de '.
                                    ConstanceService::numberToMonth(substr($subject['end_date'],5,3))
                                    .' de '. substr($subject['end_date'],0,4).', ';
                            }else{
                                $datesTeaching= $datesTeaching.substr($schoolPeriod['start_date'],8).' de '.
                                    strtoupper(ConstanceService::numberToMonth(substr($schoolPeriod['start_date'],5,3)))
                                    .' de '. substr($schoolPeriod['start_date'],0,4).' / '.
                                    substr($schoolPeriod['end_date'],8).' de '.
                                    strtoupper(ConstanceService::numberToMonth(substr($schoolPeriod['end_date'],5,3)))
                                    .' de '.substr($schoolPeriod['end_date'],0,4).', ';
                            }
                        }
                    }
                }
            }
            if ($datesTeaching!=''){
                $body[]=[
                    $teacher['first_surname'].' '.$teacher['second_surname'],
                    $teacher['first_name'].' '.$teacher['second_name'],
                    $teacher['identification'],
                    $teacher['sex'],
                    $teacher['teacher']['home_institute']==null?'':$teacher['teacher']['home_institute'],
                    $teacher['teacher']['country'],
                    substr($subjectsTeaching,0,-2),
                    substr($datesTeaching,0,-2)
                ];
            }
        }
        return $body;
    }

    public static function getGuestTeachers($schoolPeriods,$organizationId)
    {
        $sheetGuestTeachers = [];
        usort($schoolPeriods,'self::cmpSchoolPeriodStartDate');
        $title ='PROFESORES INVITADOS QUE DICTARON CLASE EN LOS SEMESTRES ';
        foreach ($schoolPeriods as $schoolPeriod){
            $title=$title.$schoolPeriod['cod_school_period'].' ';
        }
        $header= [
            [$title],
            []
        ];
        $subHeader = [
            'APELLIDOS',
            'NOMBRES',
            'CEDULA O PASAPORTE',
            'SEXO',
            'INSTITUCIÓN DE PROCEDENCIA',
            'PAÍS',
            'ACTIVIDADES REALIZADAS',
            'PERMANENCIA (FECHA QUE DICTARON CLASES)'
        ];
        $header[]=$subHeader;
        $sheetGuestTeachers[]=$header;
        $teachers = UserService::getUsers('T',$organizationId);
        $filterTeachers=self::getTeachersINV($teachers->toArray());
        $body=self::setBodyRowGuestTeachers($filterTeachers,$schoolPeriods);
        $sheetGuestTeachers[]=$body;
        return $sheetGuestTeachers;
    }
}
