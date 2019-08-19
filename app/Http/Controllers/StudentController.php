<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Student;
use App\Postgraduate;
use Illuminate\Support\Facades\Hash;


class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = User::with('student')->where('user_type','S')->get();
        if (count($students)>0){
            $studentsReturns = [];
            foreach ($students as $student){
                if ($student['student']!=null){
                    $studentsReturns[] = $student;
                }
            }
            if (count($studentsReturns)>0){
                return $studentsReturns;
            }else{
                return response()->json(['message'=>'No existen estudiantes'],206);
            }
        }else{
            return response()->json(['message'=>'No existen estudiantes'],206);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $student = User::where(['identification'=>$request['identification']])->orWhere(['email'=>$request['email']])->get();
        if (count($student)>0){//valida que el estudiante no exista
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else{
            $postgraduate = Postgraduate::find($request['postgraduate_id']);
            if ($postgraduate==null){
                return response()->json(['message'=>'Postgrado invalido'],206);
            }else{
                $request['password']=Hash::make($request['identification']);
                $request['user_type']='S';
                User::create($request->all());
                $student = User::where(['identification'=>$request['identification']])->get()[0];
                Student::create([
                    'user_id'=>$student['id'],
                    'postgraduate_id'=>$request['postgraduate_id'],
                    'student_type'=>$request['student_type'],
                    'home_university'=>$request['home_university'],
                    'current_postgraduate'=>$request['current_postgraduate'],
                    'degrees'=>$request['degrees'],
                ]);
                $teacherReturn = $teacher= User::with('student')->where('user_type','S')->where('id',$student['id'])->get()[0];
                return $teacherReturn;
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
        $student = User::with('student')->where('user_type','S')->where('id',$id)->get();
        if (count($student)>0){
            if ($student[0]['student']!=null){
                return $student;
            }else{
                return response()->json(['message'=>'Estudiante no encontrado'],206);
            }
        }else{
            return response()->json(['message'=>'Estudiante no encontrado'],206);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $student = User::with('student')->where('user_type','S')->where('id',$id)->get();
        if (count($student)>0){
            $postgraduate = Postgraduate::find($request['postgraduate_id']);
            if ($postgraduate==null){
                return response()->json(['message'=>'Postgrado invalido'],206);
            }else{
                $userEmail =  User::where(['email'=>$request['email']])->get();
                $userIdentification =  User::where(['identification'=>$request['identification']])->get();
                if (count($userEmail)>0){//preguntar si el email esta en bd
                    if ($userEmail[0]['id']==$student[0]['id']){//si son diferentes el email esta ocupado
                        if (count($userIdentification)>0){//preguntar si la identificacion esta en bd
                            if($userIdentification[0]['id']==$student[0]['id']){//si son diferentes el identificador esta ocupado por otro usuario
                                $request['user_type']='S';
                                $request['password']=$student[0]['password'];
                                $student[0]->update($request->all());
                                Student::where('user_id',$id)->get()[0]->update([
                                    'user_id'=>$id,
                                    'postgraduate_id'=>$request['postgraduate_id'],
                                    'student_type'=>$request['student_type'],
                                    'home_university'=>$request['home_university'],
                                    'current_postgraduate'=>$request['current_postgraduate'],
                                    'degrees'=>$request['degrees'],
                                ]);
                                $student = User::with('student')->where('user_type','S')->where('id',$id)->get()[0];
                                return $student;
                            }else{
                                return response()->json(['message'=>'Identificacion registrada'],206);
                            }
                        }else{// identificacion disponible
                            $request['user_type']='S';
                            $request['password']=$student[0]['password'];
                            $student[0]->update($request->all());
                            Student::where('user_id',$id)->get()[0]->update([
                                'user_id'=>$id,
                                'postgraduate_id'=>$request['postgraduate_id'],
                                'student_type'=>$request['student_type'],
                                'home_university'=>$request['home_university'],
                                'current_postgraduate'=>$request['current_postgraduate'],
                                'degrees'=>$request['degrees'],
                            ]);
                            $student = User::with('student')->where('user_type','S')->where('id',$id)->get()[0];
                            return $student;
                        }
                    }else{
                        return response()->json(['message'=>'Correo ya registrado'],206);
                    }
                }else{
                    if (count($userIdentification)>0){//preguntar si la identificacion esta en bd
                        if($userIdentification[0]['id']==$student[0]['id']){//si son diferentes el identificador esta ocupado por otro usuario
                            $request['user_type']='S';
                            $request['password']=$student[0]['password'];
                            $student[0]->update($request->all());
                            Student::where('user_id',$id)->get()[0]->update([
                                'user_id'=>$id,
                                'postgraduate_id'=>$request['postgraduate_id'],
                                'student_type'=>$request['student_type'],
                                'home_university'=>$request['home_university'],
                                'current_postgraduate'=>$request['current_postgraduate'],
                                'degrees'=>$request['degrees'],
                            ]);
                            $student = User::with('student')->where('user_type','S')->where('id',$id)->get()[0];
                            return $student;
                        }else{
                            return response()->json(['message'=>'Identificacion registrada'],206);
                        }
                    }else{// identificacion disponible
                        $request['user_type']='S';
                        $request['password']=$student[0]['password'];
                        $student[0]->update($request->all());
                        Student::where('user_id',$id)->get()[0]->update([
                            'user_id'=>$id,
                            'postgraduate_id'=>$request['postgraduate_id'],
                            'student_type'=>$request['student_type'],
                            'home_university'=>$request['home_university'],
                            'current_postgraduate'=>$request['current_postgraduate'],
                            'degrees'=>$request['degrees'],
                        ]);
                        $student = User::with('student')->where('user_type','S')->where('id',$id)->get()[0];
                        return $student;
                    }
                }
            }
        }else{
            return response()->json(['message'=>'Estudiante no encontrado'],206);
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
        $student = User::with('student')->where('user_type','S')->where('id',$id)->get();
        if (count($student)>0){
            $student[0]->delete();
            return response()->json(['message'=>'OK']);
        }else{
            return response()->json(['message'=>'Estudiante no encontrado'],206);
        }
    }
}
