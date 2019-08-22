<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SchoolPeriodStudent;
use App\Student;
use App\SchoolPeriod;

class SchoolPeriodInscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schoolPeriodInscriptions = SchoolPeriodStudent::with('schoolPeriod')->with('student')->get();
        return $schoolPeriodInscriptions;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $studentId=Student::find($request['student_id']);
        if ($studentId!=null){
            $schoolPeriodId=SchoolPeriod::find($request['school_period_id']);
            if ($schoolPeriodId!=null){
                $validRelation = true;
            }else{
                $validRelation = false;
            }
        }else{
            $validRelation = false;
        }
        if ($validRelation==false){
            return response()->json(['message'=>'Relacion errada'],206);
        }else{
            $schoolPeriodStudent = SchoolPeriodStudent::where('student_id',$request['student_id'])->where('school_period_id',$request['school_period_id'])->get();
            if (count($schoolPeriodStudent)>0){
                return response()->json(['message'=>'Inscripcion ya registrada'],206);
            }else{
                SchoolPeriodStudent::create($request->all());
                $schoolPeriodStudent = SchoolPeriodStudent::where('student_id',$request['student_id'])->where('school_period_id',$request['school_period_id'])->with('schoolPeriod')->with('student')->get();
                return $schoolPeriodStudent;
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
        $schoolPeriodInscription=SchoolPeriodStudent::find($id)->with('schoolPeriod')->with('student')->get();
        if (count($schoolPeriodInscription)>0){
            return $schoolPeriodInscription[0];
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
        $schoolPeriodInscription = SchoolPeriodStudent::find($id);
        if ($schoolPeriodInscription!=null){
            $studentId=Student::find($request['student_id']);
            if ($studentId!=null){
                $schoolPeriodId=SchoolPeriod::find($request['school_period_id']);
                if ($schoolPeriodId!=null){
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
                $inscriptionInBd = SchoolPeriodStudent::where('student_id',$request['student_id'])->where('school_period_id',$request['school_period_id'])->get();
                if (count($inscriptionInBd)>0){
                    if ($inscriptionInBd[0]['id']==$schoolPeriodInscription['id']){
                        $schoolPeriodInscription->update($request->all());
                    }else{
                        return response()->json(['message'=>'Inscripcion ya registrada'],206);
                    }
                }else{
                    $schoolPeriodInscription->update($request->all());
                }
            }
            $schoolPeriodInscription = SchoolPeriodStudent::find($id)->with('schoolPeriod')->with('student')->get();
            return $schoolPeriodInscription;
        }else{
            return response()->json(['message'=>'Inscripcion no encontrada'],206);
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
        $schoolPeriodInscription=SchoolPeriodStudent::find($id);
        if ($schoolPeriodInscription!=null){
            $schoolPeriodInscription->delete();
            return response()->json(['message'=>'OK']);
        }
        return response()->json(['message'=>'Inscripcion no encontrada'],206);
    }
}
