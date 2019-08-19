<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Teacher;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers= User::with('teacher')->where('user_type','T')->get();
        if (count($teachers)>0){
            $teachersReturns=[];
            foreach ($teachers as $teacher){
                if ($teacher['teacher']!=null){
                    $teachersReturns[] = $teacher;
                }
            }
            if (count($teachersReturns)>0){
                return $teachersReturns;
            }else{
                return response()->json(['message'=>'No existen profesores'],206);
            }
        }else{
            return response()->json(['message'=>'No existen profesores'],206);
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
        $teacher= User::where(['identification'=>$request['identification']])->orWhere(['email'=>$request['email']])->get();
        if (count($teacher)>0){//valida que el profesir no exista
            return response()->json(['message'=>'Identificacion o Correo ya registrados'],206);
        }else{
            $request['password']=Hash::make($request['identification']);
            $request['user_type']='T';
            User::create($request->all());
            $teacher = User::where(['identification'=>$request['identification']])->get()[0];
            Teacher::create([
                'user_id'=>$teacher['id'],
                'teacher_type'=>$request['teacher_type'],
            ]);
            $teacherReturn = User::with('teacher')->where('user_type','T')->where('id',$teacher['id'])->get()[0];
            return $teacherReturn;
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
        $teacher= User::with('teacher')->where('user_type','T')->where('id',$id)->get();
        if (count($teacher)>0){
            if ($teacher[0]['teacher']!=null){//valida si existe relacin entre user y teacher
                return $teacher;
            }else{
                return response()->json(['message'=>'Profesor no encontrado'],206);
            }
        }else{
            return response()->json(['message'=>'Profesor no encontrado'],206);
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
        $teacher= User::with('teacher')->where('user_type','T')->where('id',$id)->get();
        if (count($teacher)>0){
            $userEmail =  User::where(['email'=>$request['email']])->get();
            $userIdentification =  User::where(['identification'=>$request['identification']])->get();
            if (count($userEmail)>0){//preguntar si el email ya esta en bd
                if ($userEmail[0]['id']==$teacher[0]['id']){//si son diferentes el correo ya esta ocupado por otro usuario
                    if (count($userIdentification)>0){//preguntar si la identificacion existe en bd
                        if ($userIdentification[0]['id']==$teacher[0]['id']){// si son diferentes el identificador esta ocupado por otro usuario
                            $request['user_type']='T';
                            $request['password']=$teacher[0]['password'];
                            $teacher[0]->update($request->all());
                            Teacher::where('user_id',$id)->get()[0]->update(['user_id'=>$id,'teacher_type'=>$request['teacher_type']]);
                            $teacher= User::with('teacher')->where('user_type','T')->where('id',$id)->get()[0];
                            return $teacher;
                        }else{
                            return response()->json(['message'=>'Identificacion registrada'],206);
                        }
                    }else{// el identificador esta disponible Ci o rif
                        $request['user_type']='T';
                        $request['password']=$teacher[0]['password'];
                        $teacher[0]->update($request->all());
                        Teacher::where('user_id',$id)->get()[0]->update(['user_id'=>$id,'teacher_type'=>$request['teacher_type']]);
                        $teacher= User::with('teacher')->where('user_type','T')->where('id',$id)->get()[0];
                        return $teacher;
                    }
                }else{
                    return response()->json(['message'=>'Correo ya registrado'],206);
                }
            }else{//correo disponible no ocupado por otro usuario en bd
                if (count($userIdentification)>0){//preguntar si la identificacion existe en bd
                    if ($userIdentification[0]['id']==$teacher[0]['id']){// si son diferentes el identificador esta ocupado por otro usuario
                        $request['user_type']='T';
                        $request['password']=$teacher[0]['password'];
                        $teacher[0]->update($request->all());
                        Teacher::where('user_id',$id)->get()[0]->update(['user_id'=>$id,'teacher_type'=>$request['teacher_type']]);
                        $teacher= User::with('teacher')->where('user_type','T')->where('id',$id)->get()[0];
                        return $teacher;
                    }else{
                        return response()->json(['message'=>'Identificacion registrada'],206);
                    }
                }else{// el identificador esta disponible Ci o rif
                    $request['user_type']='T';
                    $request['password']=$teacher[0]['password'];
                    $teacher[0]->update($request->all());
                    Teacher::where('user_id',$id)->get()[0]->update(['user_id'=>$id,'teacher_type'=>$request['teacher_type']]);
                    $teacher= User::with('teacher')->where('user_type','T')->where('id',$id)->get()[0];
                    return $teacher;
                }
            }
        }else{
            return response()->json(['message'=>'Profesor no encontrado'],206);
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
        $teacher= User::with('teacher')->where('user_type','T')->where('id',$id)->get();
        if (count($teacher)>0){ //valida que el profesor exista par eliminarlo
            $teacher[0]->delete();
            return response()->json(['message'=>'OK']);
        }else{
            return response()->json(['message'=>'Profesor no encontrado'],206);
        }
    }
}
