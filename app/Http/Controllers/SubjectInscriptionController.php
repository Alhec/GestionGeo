<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StudentSubject;
use App\Student;
use App\SchoolPeriodSubjectTeacher;
class SubjectInscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjectInscriptions=StudentSubject::with('student')->with('subject')->get();
        if (count($subjectInscriptions)>0){
            return $subjectInscriptions;
        }
        return response()->json(['message'=>'No existen Inscripciones'],206);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $student_id=Student::find($request['student_id']);
        if ($student_id!=null){
            $subject_id=SchoolPeriodSubjectTeacher::find($request['school_period_subject_teacher_id']);
            if ($subject_id!=null){
                $validRelation = true;
            }else{
                $validRelation = false;
            }
        }else{
            $validRelation = false;
        }
        if ($validRelation == false){
            return response()->json(['message'=>'Relacion errada'],206);
        }else{
            $subjectInscription = StudentSubject::where('student_id',$request['student_id'])->where('school_period_subject_teacher_id',$request['school_period_subject_teacher_id'])->get();
            if (count($subjectInscription)>0){
                return response()->json(['message'=>'Inscripcion ya registrada'],206);
            }else{
                StudentSubject::create($request->all());
                $subjectInscription = StudentSubject::where('student_id',$request['student_id'])->where('school_period_subject_teacher_id',$request['school_period_subject_teacher_id'])->get();
                return $subjectInscription;
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subjectInscription=StudentSubject::find($id)->with('student')->with('subject')->get();
        if (count($subjectInscription)>0){
            return $subjectInscription[0];
        }
        return response()->json(['message'=>'Inscripcion no encontrada'],206);
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
        $subjectInscription=StudentSubject::find($id)->with('student')->with('subject')->get();
        if (count($subjectInscription)>0){
            $student_id=Student::find($request['student_id']);
            if ($student_id!=null){
                $subject_id=SchoolPeriodSubjectTeacher::find($request['school_period_subject_teacher_id']);
                if ($subject_id!=null){
                    $validRelation = true;
                }else{
                    $validRelation = false;
                }
            }else{
                $validRelation = false;
            }
            if ($validRelation == false){
                return response()->json(['message'=>'Relacion errada'],206);
            }else{
                $inscriptionInBd = StudentSubject::where('student_id',$request['student_id'])->where('school_period_subject_teacher_id',$request['school_period_subject_teacher_id'])->get();
                if (count($inscriptionInBd)>0){
                    if ($inscriptionInBd[0]['id']==$subjectInscription[0]['id']){
                        $subjectInscription[0]->update($request->all());
                    }else{
                        return response()->json(['message'=>'Inscripcion ya registrada'],206);
                    }
                }else{
                    $subjectInscription[0]->update($request->all());
                }
            }
            $subjectInscription=StudentSubject::find($id)->with('student')->with('subject')->get();
            return $subjectInscription;
        }
        return response()->json(['message'=>'Inscripcion no encontrada'],206);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subjectInscription=StudentSubject::find($id);
        if ($subjectInscription!=null){
            $subjectInscription->delete();
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Inscripcion no encontrada'],206);

    }
}
