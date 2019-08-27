<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SchoolPeriod;
use App\Subject;
use App\Teacher;
use App\Schedule;
use App\SchoolPeriodSubjectTeacher;
use App\Services\SchoolPeriodService;

class SchoolPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return SchoolPeriodService::getSchoolPeriods($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return SchoolPeriodService::addSchoolPeriod($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        return SchoolPeriodService::getSchoolPeriodById($request,$id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $schoolPeriod = SchoolPeriod::find($id);
        if ($schoolPeriod ==null){
            return response()->json(['message'=>'Periodo escolar no encontrado'],206);
        }else{
            $schoolPeriodInBD = SchoolPeriod::where('cod_school_period',$request['cod_school_period'])->get('id');
            if (count($schoolPeriodInBD)>0){// el cod lo posee un periodo escolar
                if ($schoolPeriodInBD[0]['id']==$schoolPeriod['id']){//validar que el cod sea del mismo periodo editando
                    if (isset($request['subject'])){
                        $subjects = $request['subject'];
                        $teachersInBd=Teacher::all('id');
                        $subjectsInBd=Subject::all('id');
                        $validRelation = true;
                        foreach ($subjects as $subject){//recorrer las relaciones para validar que la materia y el profesor asignado sean correctos
                            $foundTeacher = false;
                            foreach ($teachersInBd as $teacherInBd){//validar profesor
                                if (($subject['teacher_id']!=$teacherInBd['id'])AND $foundTeacher ==false){
                                    $validRelation = false;
                                }else{
                                    $foundTeacher=true;
                                }
                            }
                            if ($foundTeacher == true){//si se consigue el profesor entonces se validan las materias
                                $foundSubject = false;
                                foreach ($subjectsInBd as $subjectInBd){// recorrer para validar si lasmaterias estan en bd
                                    if (($subject['subject_id']!=$subjectInBd['id'])AND $foundSubject ==false){
                                        $validRelation = false;
                                    }else{
                                        $foundSubject=true;
                                        $validRelation = true;
                                    }
                                }
                            }
                        }
                        if ($validRelation == true){//si las relaciones de profesor y materia son validas
                            $schoolPeriodsSubjectsTeachersUpdates = [];//tener una lista con los postgrados actualizados para hacer un recorrido liminando los que estan en bd y no se recibieron en bd
                            foreach ($subjects as $subject){// Puede haber mas de una materia con profesor a ser actualizada
                                $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::where('teacher_id',$subject['teacher_id'])
                                    ->where('subject_id',$subject['subject_id'])->where('school_period_id',$schoolPeriod['id'])->get();
                                if (count($schoolPeriodSubjectTeacher)>0){//hay que actualizarlo
                                    $schoolPeriodSubjectTeacher[0]->update([
                                        'teacher_id'=>$subject['teacher_id'],
                                        'subject_id'=>$subject['subject_id'],
                                        'school_period_id'=>$schoolPeriod['id'],
                                        'inscription_visible'=>$subject['inscription_visible'],
                                        'limit'=>$subject['limit'],
                                        'enrolled_students'=>$subject['enrolled_students'],
                                        'load_notes'=>$subject['load_notes'],
                                        'duty'=>$subject['duty'],
                                    ]);
                                    $schoolPeriodsSubjectsTeachersUpdates[] = $schoolPeriodSubjectTeacher[0]['id'];//se agrega a la lista de los actualizados

                                    $schedulesInBD = Schedule::where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacher[0]['id'])->get();
                                    if (count($schedulesInBD)>0){//existe en bd lo eliminamos ya que no tine seguimiento de id por default
                                        foreach ($schedulesInBD as $scheduleInBD){
                                            Schedule::find($scheduleInBD['id'])->delete();
                                        }
                                    }
                                    if (isset($subject['schedule'])){
                                        foreach ($subject['schedule'] as $schedule){
                                            Schedule::Create([
                                                'school_period_subject_teacher_id'=>$schoolPeriodSubjectTeacher[0]['id'],
                                                'day'=>$schedule['day'],
                                                'classroom'=>$schedule['classroom'],
                                                'start_hour'=>$schedule['start_hour'],
                                                'end_hour'=>$schedule['end_hour'],
                                            ]);
                                        }
                                    }
                                }else{//no existe en la relacion asi que se crea
                                    SchoolPeriodSubjectTeacher::create([
                                        'teacher_id'=>$subject['teacher_id'],
                                        'subject_id'=>$subject['subject_id'],
                                        'school_period_id'=>$schoolPeriod['id'],
                                        'inscription_visible'=>$subject['inscription_visible'],
                                        'limit'=>$subject['limit'],
                                        'enrolled_students'=>$subject['enrolled_students'],
                                        'load_notes'=>$subject['load_notes'],
                                        'duty'=>$subject['duty'],
                                    ]);
                                    $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::where('teacher_id',$subject['teacher_id'])
                                        ->where('subject_id',$subject['subject_id'])->where('school_period_id',$schoolPeriod['id'])->get('id');
                                    $schoolPeriodsSubjectsTeachersUpdates[] = $schoolPeriodSubjectTeacher[0]['id'];

                                    $schedulesInBD = Schedule::where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacher[0]['id'])->get();
                                    if (count($schedulesInBD)>0){//existe en bd lo eliminamos ya que no tine seguimiento de id por default
                                        foreach ($schedulesInBD as $scheduleInBD){
                                            Schedule::find($scheduleInBD['id'])->delete();
                                        }
                                    }
                                    if (isset($subject['schedule'])){//valido si asignaron horarios a las materias
                                        $schedules = $subject['schedule'];
                                        foreach ($schedules as $schedule){
                                            Schedule::Create([
                                                'school_period_subject_teacher_id'=>$schoolPeriodSubjectTeacher[0]['id'],
                                                'day'=>$schedule['day'],
                                                'classroom'=>$schedule['classroom'],
                                                'start_hour'=>$schedule['start_hour'],
                                                'end_hour'=>$schedule['end_hour'],
                                            ]);
                                        }
                                    }
                                }
                            }
                            $schoolPeriodsSubjectsTeachers = SchoolPeriodSubjectTeacher::where('school_period_id',$schoolPeriod['id'])->get('id');
                            foreach ($schoolPeriodsSubjectsTeachers as $schoolPeriodSubjectTeacher){
                                if (!in_array($schoolPeriodSubjectTeacher['id'],$schoolPeriodsSubjectsTeachersUpdates)){
                                    $schoolPeriodSubjectTeacher->delete();
                                }
                            }
                            return $this->show($id);
                        }else{
                            return response()->json(['message'=>'Materia o profesor invalida'],206);
                        }
                    }else{
                        $schoolPeriod->update($request->all());
                        $subjectsInPeriod = SchoolPeriodSubjectTeacher::where('school_period_id',$id)->get();
                        if (count($subjectsInPeriod)>0){
                            foreach ($subjectsInPeriod as $subjectInPeriod){
                                $subjectInPeriod->delete();//eliminar porque se quito en el request si existian
                            }

                        }
                        return $this->show($id);
                    }
                }else{//el codigo esta ocupado por algun otro periodo
                    return response()->json(['message'=>'El codigo del periodo escolar ya esta registrado'],206);
                }
            }else{// el codigo esta disponible
                if (isset($request['subject'])){
                    $subjects = $request['subject'];
                    $teachersInBd=Teacher::all('id');
                    $subjectsInBd=Subject::all('id');
                    $validRelation = true;
                    foreach ($subjects as $subject){//recorrer las relaciones para validar que la materia y el profesor asignado sean correctos
                        $foundTeacher = false;
                        foreach ($teachersInBd as $teacherInBd){//validar profesor
                            if (($subject['teacher_id']!=$teacherInBd['id'])AND $foundTeacher ==false){
                                $validRelation = false;
                            }else{
                                $foundTeacher=true;
                            }
                        }
                        if ($foundTeacher == true){//si se consigue el profesor entonces se validan las materias
                            $foundSubject = false;
                            foreach ($subjectsInBd as $subjectInBd){// recorrer para validar si lasmaterias estan en bd
                                if (($subject['subject_id']!=$subjectInBd['id'])AND $foundSubject ==false){
                                    $validRelation = false;
                                }else{
                                    $foundSubject=true;
                                    $validRelation = true;
                                }
                            }
                        }
                    }
                    if ($validRelation == true){//si las relaciones de profesor y materia son validas
                        $schoolPeriodsSubjectsTeachersUpdates = [];//tener una lista con los postgrados actualizados para hacer un recorrido liminando los que estan en bd y no se recibieron en bd
                        foreach ($subjects as $subject){// Puede haber mas de una materia con profesor a ser actualizada
                            $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::where('teacher_id',$subject['teacher_id'])
                                ->where('subject_id',$subject['subject_id'])->where('school_period_id',$schoolPeriod['id'])->get();
                            if (count($schoolPeriodSubjectTeacher)>0){//hay que actualizarlo
                                $schoolPeriodSubjectTeacher[0]->update([
                                    'teacher_id'=>$subject['teacher_id'],
                                    'subject_id'=>$subject['subject_id'],
                                    'school_period_id'=>$schoolPeriod['id'],
                                    'inscription_visible'=>$subject['inscription_visible'],
                                    'limit'=>$subject['limit'],
                                    'enrolled_students'=>$subject['enrolled_students'],
                                    'load_notes'=>$subject['load_notes'],
                                    'duty'=>$subject['duty'],
                                ]);
                                $schoolPeriodsSubjectsTeachersUpdates[] = $schoolPeriodSubjectTeacher[0]['id'];//se agrega a la lista de los actualizados

                                $schedulesInBD = Schedule::where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacher[0]['id'])->get();
                                if (count($schedulesInBD)>0){//existe en bd lo eliminamos ya que no tine seguimiento de id por default
                                    foreach ($schedulesInBD as $scheduleInBD){
                                        Schedule::find($scheduleInBD['id'])->delete();
                                    }
                                }
                                if (isset($subject['schedule'])){
                                    foreach ($subject['schedule'] as $schedule){
                                        Schedule::Create([
                                            'school_period_subject_teacher_id'=>$schoolPeriodSubjectTeacher[0]['id'],
                                            'day'=>$schedule['day'],
                                            'classroom'=>$schedule['classroom'],
                                            'start_hour'=>$schedule['start_hour'],
                                            'end_hour'=>$schedule['end_hour'],
                                        ]);
                                    }
                                }
                            }else{//no existe en la relacion asi que se crea
                                SchoolPeriodSubjectTeacher::create([
                                    'teacher_id'=>$subject['teacher_id'],
                                    'subject_id'=>$subject['subject_id'],
                                    'school_period_id'=>$schoolPeriod['id'],
                                    'inscription_visible'=>$subject['inscription_visible'],
                                    'limit'=>$subject['limit'],
                                    'enrolled_students'=>$subject['enrolled_students'],
                                    'load_notes'=>$subject['load_notes'],
                                    'duty'=>$subject['duty'],
                                ]);
                                $schoolPeriodSubjectTeacher = SchoolPeriodSubjectTeacher::where('teacher_id',$subject['teacher_id'])
                                    ->where('subject_id',$subject['subject_id'])->where('school_period_id',$schoolPeriod['id'])->get('id');
                                $schoolPeriodsSubjectsTeachersUpdates[] = $schoolPeriodSubjectTeacher[0]['id'];

                                $schedulesInBD = Schedule::where('school_period_subject_teacher_id',$schoolPeriodSubjectTeacher[0]['id'])->get();
                                if (count($schedulesInBD)>0){//existe en bd lo eliminamos ya que no tine seguimiento de id por default
                                    foreach ($schedulesInBD as $scheduleInBD){
                                        Schedule::find($scheduleInBD['id'])->delete();
                                    }
                                }
                                if (isset($subject['schedule'])){//valido si asignaron horarios a las materias
                                    $schedules = $subject['schedule'];
                                    foreach ($schedules as $schedule){
                                        Schedule::Create([
                                            'school_period_subject_teacher_id'=>$schoolPeriodSubjectTeacher[0]['id'],
                                            'day'=>$schedule['day'],
                                            'classroom'=>$schedule['classroom'],
                                            'start_hour'=>$schedule['start_hour'],
                                            'end_hour'=>$schedule['end_hour'],
                                        ]);
                                    }
                                }
                            }
                        }
                        $schoolPeriodsSubjectsTeachers = SchoolPeriodSubjectTeacher::where('school_period_id',$schoolPeriod['id'])->get('id');
                        foreach ($schoolPeriodsSubjectsTeachers as $schoolPeriodSubjectTeacher){
                            if (!in_array($schoolPeriodSubjectTeacher['id'],$schoolPeriodsSubjectsTeachersUpdates)){
                                $schoolPeriodSubjectTeacher->delete();
                            }
                        }
                        return $this->show($id);
                    }else{
                        return response()->json(['message'=>'Materia o profesor invalida'],206);
                    }
                }else{
                    $schoolPeriod->update($request->all());
                    $subjectsInPeriod = SchoolPeriodSubjectTeacher::where('school_period_id',$id)->get();
                    if (count($subjectsInPeriod)>0){
                        foreach ($subjectsInPeriod as $subjectInPeriod){
                            $subjectInPeriod->delete();//eliminar porque se quito en el request si existian
                        }

                    }
                    return $this->show($id);
                }
            }


        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schoolPeriod = SchoolPeriod::find($id);
        if ($schoolPeriod!=null){
            $schoolPeriod->delete();
            return response()->json(['message'=>'Ok']);
        }else{
            return response()->json(['message'=>'Periodo escolar no encontrado'],206);
        }
    }
}
