<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Teacher;
use Illuminate\Support\Facades\Hash;
use App\Services\UserServices;
use App\Services\TeacherService;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return UserServices::getUser($request,'T');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return TeacherService::addTeacher($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        return UserServices::getUserById($request,$id,'T');
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
        return TeacherService::updateTeacher($request,$id);
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
    public function destroy($id,Request $request)
    {
        return UserServices::deleteUser($request,$id,'T');
    }
}
